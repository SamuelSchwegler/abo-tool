<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\Exception\CopyFileException;
use PhpOffice\PhpWord\Exception\CreateTemporaryFileException;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class OrderController extends Controller
{
    /**
     * Lieferschein erstellen
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     */
    public function exportDeliveryNote(Order $order): BinaryFileResponse
    {
        return response()->download($order->exportDeliveryNote());
    }
}
