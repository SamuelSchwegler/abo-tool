<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Customer extends Model
{
    use HasFactory;

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany {
        return $this->hasMany(Order::class);
    }

    public function buys(): HasMany {
        return $this->hasMany(Buy::class);
    }

    public function productBuys(): Collection
    {
        return DB::table('buys')->where('buys.customer_id', $this->id)
            ->leftJoin('bundles', 'buys.bundle_id', 'bundles.id')
            ->leftJoin('products','products.id', 'bundles.product_id')
            ->groupBy('products.id')
            ->selectRaw('products.id as product_id, products.name, SUM(bundles.deliveries) as total_deliveries')->get();
    }

    public function productOrders(): Collection
    {
        return DB::table('orders')->where('orders.customer_id', $this->id)
            ->leftJoin('products','products.id', 'orders.product_id')
            ->leftJoin('deliveries', 'deliveries.id', 'orders.delivery_id')
            ->where('canceled', 0)
            ->groupBy('orders.product_id')
            ->selectRaw('products.id as product_id, products.name, COUNT(orders.id) as ordered, SUM(IF(deliveries.deadline >= NOW(), 1, 0)) AS planned')->get();
    }

    /**
     * Zusammenfassung über Kontostand für die Produkte (noch offene Lieferungen)
     * @return Collection
     */
    public function productBalances(): Collection
    {
        $balances = [];
        foreach($this->productBuys() as $buy) {
            $order = $this->productOrders()->where('product_id', $buy->product_id)->first();
            $balance = $buy;
            $balance->total_deliveries = (int) $buy->total_deliveries;
            $balance->ordered = (int) $order->ordered;
            $balance->planned = (int) $order->planned;
            $balance->balance = $balance->total_deliveries - $balance->ordered;

            // todo calculate Expected end
            $balances[] = $balance;
        }

        return collect($balances);
    }

    public function next_orders(): HasMany {
        return $this->orders()->whereHas('delivery', function ($query) {
            $query->where('date', '>=', now()->subDay());
        })->limit(Order::PREVIEW_OFFSET);
    }

    public function getNameAttribute(): string {
        return $this->first_name.' '.$this->last_name;
    }

    public function billing_address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }
}
