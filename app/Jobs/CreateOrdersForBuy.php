<?php

namespace App\Jobs;

use App\Models\Buy;
use App\Models\Customer;
use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use function PHPUnit\Framework\assertNotNull;

class CreateOrdersForBuy implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Buy $buy;
    protected Customer $customer;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Buy $buy)
    {
        $this->buy = $buy;
        $this->customer = $buy->customer;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // richtiger Delivery Service finden
        $delivery_service = $this->customer->delivery_service();

        $deliveries = $delivery_service->deliveries()->where('deadline', '>=', now()->addDay())->get();
        $count = 0;
        $max_orders = $this->buy->bundle->deliveries;
        foreach ($deliveries as $delivery) {
            Order::create([
                'customer_id' => $this->customer->id,
                'delivery_id' => $delivery->id,
                'product_id' => $this->buy->bundle->product->id
            ]);

            $count++;
            if($count >= $max_orders) {
                break; // nicht mehr Orders erstellen, als im Abo sind...
            }
        }
    }
}