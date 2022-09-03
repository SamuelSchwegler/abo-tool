<?php

namespace App\Jobs;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateOrders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Customer $customer;
    protected Product $product;
    protected Carbon $from;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Customer $customer, Product $product, ?Carbon $from = null)
    {
        $this->customer = $customer;
        $this->product = $product;
        $this->from = is_null($from) ? now()->addDay() : $from;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @changes v0.1.2 - nur bis zum ready to order generieren
     */
    public function handle()
    {
        // richtiger Delivery Service finden
        $delivery_service = $this->customer->delivery_service();

        // Erstelle Orders auch über Deadline hinaus.
        $deliveries = $delivery_service?->deliveries()->where('date', '>=', $this->from)->readyToOrder()->get() ?? [];
        $count = 0;
        $max_orders = $this->customer->creditOfProduct($this->product, true);

        $orders = [];
        foreach ($deliveries as $delivery) {
            $order = Order::where('customer_id', $this->customer->id)->where('delivery_id', $delivery->id)
                ->where('product_id', $this->product->id)->first();

            if ($count < $max_orders) {
                if (is_null($order)) {
                    $order = Order::create([
                        'customer_id' => $this->customer->id,
                        'delivery_id' => $delivery->id,
                        'product_id' => $this->product->id,
                    ]);

                    $count++;
                }
            }

            if (! is_null($order)) {
                if (! $order->affordable) {
                    $order->update([
                        'affordable' => true,
                    ]);
                }
                $orders[] = $order;
            } else {
                break;
            }
        }

        // Nicht leistbares Markieren
        if ($max_orders < 0) {
            $non_affordable = collect(array_reverse($orders));
            $count = 0;

            foreach ($non_affordable as $order) {
                if ($count < abs($max_orders)) {
                    if (! $order->canceled) {
                        $order->update([
                            'affordable' => false,
                        ]);
                        $count++;
                    } elseif (! $order->affordable) {
                        $order->update([
                            'affordable' => true,
                        ]);
                    }

                } else {
                    break;
                }
            }
        }
    }
}
