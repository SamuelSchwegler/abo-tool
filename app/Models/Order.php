<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\Exception\CopyFileException;
use PhpOffice\PhpWord\Exception\CreateTemporaryFileException;
use PhpOffice\PhpWord\TemplateProcessor;

class Order extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    const PREVIEW_OFFSET = 10; // wie viele Lieferungen voraus werden Orders angezeigt.

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function deadlinePassed(): bool
    {
        return $this->delivery->deadlinePassed();
    }

    public function deliveryNoteName(): string
    {
        return str_replace(' ', '_', $this->customer->name) . '_' . $this->id . '.docx';
    }

    /**
     * @return string Pfad zum File
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     */
    public function exportDeliveryNote(): string
    {
        $path = storage_path('app/templates/delivery_note.docx');
        $template = new TemplateProcessor($path);

        $customer = $this->customer;
        $billing_address = $customer->billing_address;

        $template->setValue('customer_name', $customer->name);
        $template->setValue('customer_street', $billing_address->street);
        $template->setValue('customer_postcode', $billing_address->postcode);
        $template->setValue('customer_city', $billing_address->city);

        // Items
        $item_rows = [];
        foreach($this->delivery->items as $item) {
            $item_rows[] = [
                'item_count' => 1,
                'item_name' => $item->name,
                'item_origin' => $item->item_origin->name
            ];
        }
        $template->cloneRowAndSetValues('item_count', $item_rows);

        // todo: soll es PDF sein?
        $output = storage_path('app/delivery-notes/delivery-note_' . $this->id . '.docx');
        $template->saveAs($output);

        return $output;
    }
}
