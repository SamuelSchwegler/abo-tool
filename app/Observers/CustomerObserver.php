<?php

namespace App\Observers;

use App\Jobs\CustomerChangeDelivery;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;

class CustomerObserver
{
    public function updated(Customer $customer): void
    {
        if($customer->isDirty('depository')) {
            Order::withoutEvents(function () use ($customer) {
                $old = $customer->getOriginal('depository');
                $new = $customer->depository;

                $customer->orders()->whereHas('delivery', function ($query) {
                    $query->where('date', '>=', now()->subDay());
                })->where(function (Builder $query) use ($old) {
                    $query->where('depository', $old)->orWhereNull('depository');
                })->update([
                    'depository' => $new,
                ]);
            });
        }

        if($customer->isDirty('delivery_service_id') || $customer->isDirty('delivery_address_id')) {
            CustomerChangeDelivery::dispatch($customer)->onQueue('default');
        }
    }
}
