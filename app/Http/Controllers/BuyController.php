<?php

namespace App\Http\Controllers;

use App\Http\Resources\BuyResource;
use App\Models\Bundle;
use App\Models\Buy;
use App\Models\Setting;
use App\Notifications\SendInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use TCPDF;
use Sprain\SwissQrBill as QrBill;

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
        $path = storage_path('app/bills/bill_' . $buy->id . '.pdf');

        $tcPdf->Output($path, 'F');

        return response()->download($path);
    }

    public function getHeader(Buy $buy)
    {
        $setting = Setting::first();
        $customer = $buy->customer;
        $billing_address = $customer->billing_address;
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
                    'price' => number_format($buy->price / 100, 2, '.', '\'') . ' CHF'
                ],
                [
                    'name' => 'Lieferkosten',
                    'price' => '12.00 CHF'
                ]
            ],
            'meta' => [
                'issue_date' => $buy->issued->format('d.m.Y'),
                'total_price' => number_format(($buy->price / 100) + 12, 2, '.', '\'') . ' CHF'
            ]
        ])->render();
    }
}
