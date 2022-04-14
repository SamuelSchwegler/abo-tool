<?php

namespace App\Models;

use App\Traits\TraitUuid;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Sprain\SwissQrBill as QrBill;

class Buy extends Model
{
    use HasFactory, TraitUuid;

    protected $guarded = ['id'];
    protected $dates = ['issued'];

    public function bundle()
    {
        return $this->belongsTo(Bundle::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function createBill(): QrBill\QrBill
    {
        $setting = Setting::first();
        $customer = $this->customer;

        $qrBill = QrBill\QrBill::create();

        // Add creditor information
        // Who will receive the payment and to which bank account?
        $qrBill->setCreditor(
            QrBill\DataGroup\Element\CombinedAddress::create(
                $setting->name,
                $setting->address->street,
                $setting->address->postcode . ' ' . $setting->address->city,
                'CH'
            ));

        $qrBill->setCreditorInformation(
            QrBill\DataGroup\Element\CreditorInformation::create(
                'CH5330790016597781328' // This is a special QR-IBAN. Classic IBANs will not be valid here.
            ));

        // Add debtor information
        // Who has to pay the invoice? This part is optional.
        //
        // Notice how you can use two different styles of addresses: CombinedAddress or StructuredAddress.
        // They are interchangeable for creditor as well as debtor.
        $qrBill->setUltimateDebtor(
            QrBill\DataGroup\Element\StructuredAddress::createWithStreet(
                $customer->name,
                $customer->billing_address->street,
                null,
                $customer->billing_address->postcode,
                $customer->billing_address->city,
                'CH'
            ));

        // Add payment amount information
        // What amount is to be paid?
        $qrBill->setPaymentAmountInformation(
            QrBill\DataGroup\Element\PaymentAmountInformation::create(
                'CHF',
                $this->price / 100
            ));

        // Add payment reference
        // This is what you will need to identify incoming payments.
        $referenceNumber = QrBill\Reference\QrPaymentReferenceGenerator::generate(
            '210000',  // You receive this number from your bank (BESR-ID). Unless your bank is PostFinance, in that case use NULL.
            '313947143000901' // A number to match the payment with your internal data, e.g. an invoice number
        );

        $qrBill->setPaymentReference(
            QrBill\DataGroup\Element\PaymentReference::create(
                QrBill\DataGroup\Element\PaymentReference::TYPE_QR,
                $referenceNumber
            ));

        // Optionally, add some human-readable information about what the bill is for.
        $qrBill->setAdditionalInformation(
            QrBill\DataGroup\Element\AdditionalInformation::create(
                'Rechnung ' . $this->id
            )
        );

        // Now get the QR code image and save it as a file.
        $qr_code_path = storage_path('app/bills/qr_codes');

        try {
            $qrBill->getQrCode()->writeFile($qr_code_path . '/qr.png');
            $qrBill->getQrCode()->writeFile($qr_code_path . '/qr.svg');
        } catch (Exception $e) {
            foreach ($qrBill->getViolations() as $violation) {
                echo $violation->getMessage() . "\n";
            }

            echo $e->getMessage(); // vorangehend wird nur anderes zeugs gemacht...
            exit;
        }

        return $qrBill;
    }

    public function getFormatedPriceAttribute(): string
    {
        return number_format($this->price / 100, 2, '.', '\'');
    }
}
