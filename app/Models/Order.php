<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\Exception\CopyFileException;
use PhpOffice\PhpWord\Exception\CreateTemporaryFileException;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
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
        $delivery_address = $customer->delivery_address;
        $billing_address = $customer->billing_address;

        $template->setValue('today', now()->format('d.m.Y'));
        $template->setValue('delivery_service', $this->delivery->delivery_service->name);
        $template->setValue('delivery_date', $this->delivery->date->format('d.m.Y'));
        $template->setValue('depository', $this->depository);
        $template->setValue('product_name', $this->product->name);

        $balances = $customer->productBalances();
        $balance = array_key_exists($this->product_id, $balances) ? $balances[$this->product_id]->balance : 0;
        $template->setValue('open_deliveries', $balance);

        $settings = Setting::first();
        $template->setValue('company_postcode', $settings->address->city);
        $template->setValue('company_city', $settings->address->city);

        $template->setValue('customer_name', $customer->name);
        $template->setValue('customer_street', $delivery_address?->street ?? $billing_address->street);
        $template->setValue('customer_postcode', $delivery_address?->postcode ?? $billing_address->postcode);
        $template->setValue('customer_city', $delivery_address?->city ?? $billing_address->city);

        // Items
        $item_rows = [];
        foreach ($this->delivery->items as $item) {
            $item_rows[] = [
                'item_name' => $item->name,
                'item_origin' => $item->item_origin->name
            ];
        }
        $template->cloneRowAndSetValues('item_name', $item_rows);

        // todo: soll es PDF sein?
        $word_output = storage_path('app/delivery-notes/delivery-note_' . $this->id . '.docx');
        $template->saveAs($word_output);

        return $word_output;
    }
}
