<?php

namespace App\Http\Controllers;

use App\Http\Resources\BuyResource;
use App\Models\Bundle;
use App\Models\Buy;
use App\Notifications\SendInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use TCPDF;
use Sprain\SwissQrBill as QrBill;

class BuyController extends Controller
{
    public function contact(Bundle $bundle)
    {
        $user = Auth::user();
        if (is_null($user)) {
            // todo login
        } else {
            $customer = $user->customer;

            $buy = Buy::create([
                'customer_id' => $customer->id,
                'bundle_id' => $bundle->id,
                'price' => $bundle->price
            ]);

            return view('buy.contact')->with([
                'buy' => $buy,
                'bundle' => $bundle,
                'customer' => $buy->customer,
            ]);
        }
    }

    public function contactSubmit()
    {
        // todo allenfalls konto erstellen
    }

    public function payment(Buy $buy)
    {
        return view('buy.payment')->with([
            'buy' => $buy,
            'bundle' => $buy->bundle,
            'customer' => $buy->customer,
        ]);
    }

    public function paymentSubmit(Buy $buy, Request $request) {
        $buy->customer->user->notify(new SendInvoice($buy));

        return redirect(route('buy.confirmation', $buy));
    }

    public function confirmation(Buy $buy)
    {
        return view('buy.confirmation')->with([
            'buy' => $buy,
            'bundle' => $buy->bundle,
            'customer' => $buy->customer,
        ]);
    }

    public function managePayments() {
        return view('buy.payments')->with([
            'buys' => BuyResource::collection(Buy::all()),
        ]);
    }

    public function exportBill(Buy $buy) {
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
