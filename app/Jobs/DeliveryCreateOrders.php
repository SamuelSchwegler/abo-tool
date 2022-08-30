<?php

namespace App\Jobs;

use App\Models\Delivery;
use App\Models\DeliveryService;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeliveryCreateOrders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Delivery $delivery;
    protected DeliveryService $service;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Delivery $delivery)
    {
        $this->delivery = $delivery;
        $this->service = $delivery->delivery_service;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @changes v0.1.2 credit of product muss die geplanten Lieferungen einbeziehen
     */
    public function handle()
    {
        $customers = $this->service->customers();
        foreach ($customers as $customer) {
            foreach ($customer->productBalances() ?? [] as $balance) {
                $credit = $customer->creditOfProduct(Product::find($balance->product_id), true);
                if ($credit > 0) {
                    $order = Order::where('customer_id', $customer->id)->where('delivery_id', $this->delivery->id)
                        ->where('product_id', $balance->product_id)->first();

                    if (is_null($order)) {
                        $order = Order::create([
                            'customer_id' => $customer->id,
                            'delivery_id' => $this->delivery->id,
                            'product_id' => $balance->product_id,
                        ]);
                    }
                }

            }

        }
    }
}
