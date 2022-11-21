<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        return $this->hasMany(Order::class)->isActive();
    }

    /**
     * v0.1.0.
     *
     * @return bool
     * @changes - v0.1.1 - Ende des Tages soll inkludiert werden
     */
    public function deadlinePassed(): bool
    {
        return $this->deadline->endOfDay()->lte(now());
    }

    /**
     * v0.1.1.
     *
     * @param $query
     * @return mixed
     */
    public function scopeDeadlineNotPassed($query): mixed
    {
        return $query->where('deadline', '>=', now()->startOfDay()->format('Y-m-d'));
    }

    public static function deadlineOnNextDay()
    {
        return self::deadlineNotPassed()->where('deadline', '<=', now()->addDay()->endOfDay()->format('Y-m-d H:i:s'))->get();
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'delivery_product_items');
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

    public function copyItemsFromDate(Product $product): void
    {
        $pivots = DeliveryProductItem::whereDate('date', $this->date->format('Y-m-d'))->where('product_id', $product->id)->get();
        foreach ($pivots as $pivot) {
            DeliveryProductItem::firstOrCreate([
                'delivery_id' => $this->id,
                'product_id' => $pivot->product_id,
                'item_id' => $pivot->item_id,
            ]);
        }
    }

    /**
     * Ist eine Lieferung im Zeitraum, in dem man bestellen kann?
     *
     * @param $query
     * @param $date
     * @return mixed
     */
    public function scopeReadyToOrder($query, ?Carbon $date = null)
    {
        if(is_null($date)) {
            $date = now();
        }
        return $query->where('date', '>=', $date->copy()->subDay())
            ->where('date', '<=', now()->addDays(Order::PREVIEW_OFFSET_DAYS))
            ->where('approved', '=', 1);
    }
}
