<?php

namespace App\Models;

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
        return $this->hasMany(Order::class)->where('canceled', 0)
            ->where('affordable', 1);
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

    public static function deadlineOnNextDay() {
        return self::deadlineNotPassed()->where('deadline', '<=', now()->addDay()->endOfDay()->format('Y-m-d H:i:s'))->get();
    }

    public function items() {
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
}
