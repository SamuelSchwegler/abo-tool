<?php

namespace App\Jobs;

use App\Models\Customer;
use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CustomerChangeDelivery implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Customer $customer;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        Order::withoutEvents(function () {
            $orders = $this->customer->orders()->whereHas('delivery', function ($query) {
                $query->where('deadline', '>=', now()->startOfDay()->format('Y-m-d H:i:s'));
            })->get();

            $delivery_service = $this->customer->delivery_service();

            foreach ($orders as $order) {
                $delivery = Delivery::where('delivery_service_id', $delivery_service->id)
                    ->whereRaw('date(date) like "' . $order->delivery->date->format('Y-m-d') . '"')
                    ->where('approved', 1)
                    ->first();

                if (!is_null($delivery)) {
                    $order->update(['delivery_id' => $delivery->id]);
                }
            }
        });
    }
}
