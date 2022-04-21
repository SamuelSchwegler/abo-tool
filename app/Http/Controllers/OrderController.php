<?php

namespace App\Http\Controllers;

use App\Models\Order;
use PhpOffice\PhpWord\Exception\CopyFileException;
use PhpOffice\PhpWord\Exception\CreateTemporaryFileException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class OrderController extends Controller
{
    /**
     * Lieferschein erstellen.
     *
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     */
    public function exportDeliveryNote(Order $order): BinaryFileResponse
    {
        return response()->download($order->exportDeliveryNote());
    }
}
