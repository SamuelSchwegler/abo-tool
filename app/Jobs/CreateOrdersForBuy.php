<?php

namespace App\Jobs;

use App\Models\Buy;
use App\Models\Customer;
use App\Models\Delivery;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateOrdersForBuy implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Buy $buy;
    protected Customer $customer;
    protected Carbon $from;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Buy $buy, ?Carbon $from = null)
    {
        $this->buy = $buy;
        $this->customer = $buy->customer;
        $this->from = is_null($from) ? now()->addDay() : $from;
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

        // Erstelle Orders auch über Deadline hinaus.
        $deliveries = $delivery_service->deliveries()->where('date', '>=', $this->from)->where('approved', 1)->get();
        $count = 0;
        $max_orders = $this->customer->creditOfProduct($this->buy->bundle->product, true);
        foreach ($deliveries as $delivery) {
            $order = Order::where('customer_id',$this->customer->id)->where('delivery_id',$delivery->id)
                ->where('product_id', $this->buy->bundle->product->id)->first();

            if(is_null($order)) {
                Order::create([
                    'customer_id' => $this->customer->id,
                    'delivery_id' => $delivery->id,
                    'product_id' => $this->buy->bundle->product->id,
                ]);

                $count++;

                if($count >= $max_orders) {
                    break; // nicht mehr Orders erstellen, als im Abo sind...
                }
            }


        }
    }
}
