<?php

namespace App\Http\Controllers;

use App\Http\Resources\BuyResource;
use App\Models\Bundle;
use App\Models\Buy;
use App\Notifications\SendInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use TCPDF;
use Sprain\SwissQrBill as QrBill;

class BuyController extends Controller
{
    public function exportBill(Buy $buy) {
        Log::info($buy);
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $qrBill = $buy->createBill();

        $tcPdf = new TCPDF('P', 'mm', 'A4', true, 'ISO-8859-1');
        $tcPdf->setPrintHeader(false);
        $tcPdf->setPrintFooter(false);
        $tcPdf->AddPage();

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
}
