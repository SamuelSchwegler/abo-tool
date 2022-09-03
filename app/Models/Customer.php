<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Customer extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $guarded = ['id'];

    protected $casts = [
        'used_orders' => 'array',
        'active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        // todo sortieren
        return $this->hasMany(Order::class);
    }

    public function buys(): HasMany
    {
        return $this->hasMany(Buy::class)->orderByDesc('created_at');
    }

    public function productBuys(?Product $product = null): Collection
    {
        $query = DB::table('buys')->where('buys.customer_id', '=', $this->id)->where('buys.paid', 1)
            ->leftJoin('bundles', 'buys.bundle_id', 'bundles.id')
            ->leftJoin('products', 'products.id', 'bundles.product_id');

        if (! is_null($product)) {
            $query = $query->where('products.id', $product->id);
        }

        return $query->groupBy('products.id')
            ->selectRaw('products.id as product_id, products.name, SUM(bundles.deliveries) as total_deliveries, max(issued) as last_issue')->get();
    }

    public function productOrders(?Product $product = null): Collection
    {
        $query = DB::table('orders')->where('orders.customer_id', $this->id)
            ->leftJoin('products', 'products.id', 'orders.product_id')
            ->leftJoin('deliveries', 'deliveries.id', 'orders.delivery_id')
            ->where('canceled', 0);

        if (! is_null($product)) {
            $query = $query->where('products.id', $product->id);
        }

        return $query->groupBy('products.id')
            ->selectRaw('products.id as product_id, products.name, COUNT(orders.id) as ordered, SUM(IF(deliveries.deadline >= NOW(), 1, 0)) AS planned')->get();
    }

    /**
     * Zusammenfassung über Kontostand für die Produkte (noch offene Lieferungen).
     *
     * @return array
     */
    public function productBalances(): array
    {
        $balances = [];
        foreach ($this->productBuys() as $buy) {
            $order = $this->productOrders(Product::find($buy->product_id))->first();
            $balance = $buy;
            $balance->ordered_before = 0;
            $balance->total_deliveries = (int) $buy->total_deliveries;
            $balance->ordered = (int) $order?->ordered ?? 0;
            $balance->planned = (int) $order?->planned ?? 0;
            $balance->balance = $balance->total_deliveries - $balance->ordered;
            $balance->last_issue = Carbon::parse($buy->last_issue);
            $balances[$buy->product_id] = $balance;
        }

        foreach ($this->used_orders ?? [] as $key => $used) {
            if (array_key_exists((int) $key, $balances)) {
                $balance = $balances[(int) $key];
                $balance->ordered_before = $used;
                $balance->ordered += $used;
                $balance->balance -= $used;
                $balances[(int) $key] = $balance;
            } else {
                Log::error('balance key '.$key.' not existing for customer: '.$this->id);
            }
        }

        /*
        foreach ($balances as $key => $balance) {
            if ($balance->last_issue->lt(now()->subMonths(6)) && $balance->balance === 0 && $balance->planned === 0) {
                unset($balances[$key]);
            }
        }
        */

        return $balances;
    }

    /**
     * Wie viel Guthaben für ein Produkt besteht noch?
     *
     * @param  Product  $product
     * @param  bool  $with_planned
     * @return int
     */
    public function creditOfProduct(Product $product, bool $with_planned = false): int
    {
        $buys = $this->productBuys($product)->first();
        $orders = $this->productOrders($product)->first();

        $balance = $buys?->total_deliveries ?? 0;

        if (! is_null($orders)) {
            if ($with_planned) {
                $balance -= $orders->ordered;
            } else {
                $balance -= $orders->ordered - $orders->planned;
            }
        }

        // bereits bestellt
        if (! is_null($this->used_orders) && array_key_exists($product->id, $this->used_orders)) {
            $balance -= $this->used_orders[$product->id];
        }

        return $balance;
    }

    /**
     * v0.1.0
     * Gibt die nächsten Bestellungen Zurück.
     *
     * @return HasMany
     */
    public function next_orders(): HasMany
    {
        return $this->orders()->whereHas('delivery', function ($query) {
            $query->readyToOrder();
        })->join('deliveries', 'deliveries.id', '=', 'orders.delivery_id')
            ->orderBy('deliveries.date')
            ->select('orders.*');
    }

    public function getNameAttribute(): string
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function getDeliveryOptionAttribute(): string
    {
        $delivery = $this->delivery_address;
        $billing = $this->billing_address;
        if (is_null($delivery)) {
            return 'pickup';
        } elseif ($delivery->id === $billing?->id) {
            return 'match';
        } else {
            return 'split';
        }
    }

    public function delivery_address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'delivery_address_id');
    }

    public function billing_address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    /**
     * Gibt Lieferservice für Adresse des Kundens, falls diese nicht in den Lieferzonen ist wird Selbstabholung gewählt.
     *
     * @return DeliveryService|null
     */
    public function delivery_service(): ?DeliveryService
    {
        $postcode = $this->delivery_address?->postcode;
        if (is_null($postcode)) {
            return DeliveryService::find($this->delivery_service_id);
        }

        return DeliveryService::findServiceForPostcode($postcode);
    }

    public static function rules(): array
    {
        return [
            'active' => ['nullable', 'boolean'],
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'company_name' => ['nullable', 'string'],
            'phone' => ['required', 'string'],
            'pickup' => ['nullable', 'boolean'],
            'depository' => ['nullable', 'string'],
            'delivery_address_id' => ['nullable', 'exists:addresses,id'],
            'billing_address_id' => ['nullable', 'exists:addresses,id'],
            'internal_comment' => ['nullable', 'string'],
            'discount' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }
}
