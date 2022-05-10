<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class Delivery extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $dates = ['date', 'deadline'];

    public function delivery_service(): BelongsTo
    {
        return $this->belongsTo(DeliveryService::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function active_orders(): HasMany
    {
        return $this->hasMany(Order::class)->where('canceled', 0);
    }

    public function deadlinePassed(): bool
    {
        return $this->deadline->lt(now());
    }

    public function items() {
        return $this->belongsToMany(Item::class);
    }

    /**
     * @return string Pfad des Zip Ordners
     */
    public function exportDeliveryNotes(): string
    {
        $path = storage_path('app/delivery-notes/delivery-notes_'.$this->id.'.zip');
        if (file_exists($path)) {
            unlink($path);
        }

        $zip = new ZipArchive();
        if ($zip->open($path, ZipArchive::CREATE) === true) {
            foreach ($this->active_orders as $order) {
                $zip->addFile($order->exportDeliveryNote(), $order->deliveryNoteName());
            }
            $zip->close();
        }

        return $path;
    }
}
