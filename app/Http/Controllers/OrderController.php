<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\Exception\CopyFileException;
use PhpOffice\PhpWord\Exception\CreateTemporaryFileException;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class OrderController extends Controller
{
    public function customer()
    {
        $customer = Auth::user()?->customer;

        return view('deliveries')->with([
            'next_deliveries' => $customer?->next_orders ?? []
        ]);
    }

    /**
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     */
    public function exportDeliveryNote(Order $order): BinaryFileResponse
    {
        $path = storage_path('app/templates/delivery_note.docx');
        $template = new TemplateProcessor($path);

        $customer = $order->customer;
        $billing_address = $customer->billing_address;

        $template->setValue('customer_name', $customer->name);
        $template->setValue('customer_street', $billing_address->street);
        $template->setValue('customer_postcode', $billing_address->postcode);
        $template->setValue('customer_city', $billing_address->city);


        $output = storage_path('app/delivery-notes/output.docx');
        $template->saveAs($output);

        return response()->download($output);
    }
}
