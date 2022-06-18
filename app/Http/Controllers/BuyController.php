<?php

namespace App\Http\Controllers;

use App\Models\Buy;
use App\Models\Setting;
use Sprain\SwissQrBill as QrBill;
use TCPDF;

class BuyController extends Controller
{
    public function exportBill(Buy $buy)
    {
        $qrBill = $buy->createBill();

        $tcPdf = new TCPDF('P', 'mm', 'A4', 'UTF-8', 'ISO-8859-1');
        $tcPdf->setPrintHeader(false);
        $tcPdf->setPrintFooter(false);
        $tcPdf->AddPage();

        // create some HTML content
        $header = $this->getHeader($buy);

        // output the HTML content
        $tcPdf->writeHTML($header, true, false, true, false, '');

        // 3. Create a full payment part for TcPDF
        $output = new QrBill\PaymentPart\Output\TcPdfOutput\TcPdfOutput($qrBill, 'de', $tcPdf);
        $output
            ->setPrintable(false)
            ->getPaymentPart();

        // 4. For demo purposes, let's save the generated example in a file
        $path = storage_path('app/bills/bill_'.$buy->id.'.pdf');

        $tcPdf->Output($path, 'F');

        return response()->download($path);
    }

    public function getHeader(Buy $buy)
    {
        $setting = Setting::first();
        $customer = $buy->customer;
        $billing_address = $customer->billing_address;

        $summary_text = 'Sämtliche Produkte sind Bio-zertifiziert: Bio-Zertifizierung: CH-BIO 006 | Betrieb-Nr: 1396
Steuernummer: 109.681.257';

        return view('export.bill_head')->with([
            'issuer' => [
                'name' => $setting->name,
                'street' => $setting->address->street,
                'postcode' => $setting->address->postcode,
                'city' => $setting->address->city,
            ],
            'receiver' => [
                'name' => $customer->name,
                'street' => $billing_address->street,
                'postcode' => $billing_address->postcode,
                'city' => $billing_address->city,
            ],
            'items' => [
                [
                    'name' => $buy->bundle->name,
                    'single_price' => 'CHF '.number_format($buy->price / $buy->bundle->deliveries, 2, '.', '\''),
                    'total_price' => 'CHF '.number_format($buy->price, 2, '.', '\''),
                    'count' => $buy->bundle->deliveries,
                ],
                [
                    'name' => 'Lieferkosten',
                    'single_price' => 'CHF '.number_format($buy->delivery_cost / $buy->bundle->deliveries, 2, '.', '\''),
                    'total_price' => 'CHF '.$buy->delivery_cost,
                    'vat' => '7.70',
                    'count' => $buy->bundle->deliveries,
                ],
            ],
            'meta' => [
                'issue_date' => $buy->issued->format('d.m.Y'),
                'total_price' => 'CHF '.$buy->total_cost,
                'total_vat' => 'CHF '.$buy->total_vat,
                'discount_percent' => $buy->discount_percent,
                'discount_amount' => 'CHF '.$buy->discount_amount,
                'summary_text' => $summary_text,
            ],
        ])->render();
    }
}
