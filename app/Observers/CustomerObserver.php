<?php

namespace App\Observers;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;

class CustomerObserver
{
    public function updated(Customer $customer)
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
    }
}
