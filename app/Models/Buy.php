<?php

namespace App\Models;

use App\Traits\TraitUuid;
use Exception;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
                $this->total_cost
            ));

        // Add payment reference
        // This is what you will need to identify incoming payments.
        $referenceNumber = QrBill\Reference\QrPaymentReferenceGenerator::generate(
            $setting->besr_id,  // You receive this number from your bank (BESR-ID). Unless your bank is PostFinance, in that case use NULL.
            $this->bill_number // A number to match the payment with your internal data, e.g. an invoice number
        );

        $qrBill->setPaymentReference(
            QrBill\DataGroup\Element\PaymentReference::create(
                QrBill\DataGroup\Element\PaymentReference::TYPE_QR,
                $referenceNumber
            ));

        // Optionally, add some human-readable information about what the bill is for.
        $qrBill->setAdditionalInformation(
            QrBill\DataGroup\Element\AdditionalInformation::create(
                'Rechnungsnr: ' . $this->bill_number
            )
        );

        // Now get the QR code image and save it as a file.
        $qr_code_path = storage_path('app/bills/qr_codes');
        if (!is_dir($qr_code_path)) {
            $dirs = explode('/', $qr_code_path);
            $path = '';
            foreach ($dirs as $dir) {
                $path .= $dir . '/';
                if (!is_dir($path)) {
                    mkdir($path, '0770', true);
                    chmod($path, 02775);
                }
            }
        }

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
        return number_format($this->price, 2, '.', '\'');
    }

    /**
     * @return Attribute
     */
    protected function deliveryCost(): Attribute
    {
        return Attribute::make(
            get: fn($value) => number_format($value / 100, 2, '.', '\''),
            set: fn($value) => $value * 100,
        );
    }

    /**
     * @return Attribute
     */
    protected function price(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value / 100,
            set: fn($value) => $value * 100,
        );
    }

    /**
     * @return string
     */
    protected function getTotalCostAttribute(): string
    {
        $amount = (($this->delivery_cost + $this->price) * (100 - $this->discount_percent) / 100);
        return number_format($amount, 2, '.', '\'');
    }

    public function getTotalVatAttribute(): string
    {
        $value = $this->delivery_cost * 0.077;

        return number_format($value, 2, '.', '\'');
    }

    public function getDiscountPercentAttribute(): string
    {
        return number_format($this->customer->discount, 1, '.', '\'');
    }

    public function getDiscountAmountAttribute(): string
    {
        return number_format(-1 * $this->price * ($this->discount_percent / 100), 2, '.', '\'');
    }
}
