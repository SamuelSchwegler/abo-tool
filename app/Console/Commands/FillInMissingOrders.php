<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use App\Notifications\FixOrdersNotification;
use Illuminate\Console\Command;

class FillInMissingOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:fill-in-missing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $customers = Customer::orderBy('last_name')->get();
        foreach ($customers as $customer) {
            $fixes = false;
            $delivery_service = $customer->delivery_service();
            if (! is_null($delivery_service)) {
                // todo get max of relation
                $max_order_date = $customer->deliveries()->max('date');
                $max_order = $customer->orders()->whereHas('delivery', function ($q) use ($max_order_date) {
                    $q->where('date', '=', $max_order_date);
                })->first();

                if ($max_order_date > now()->format('Y-m-d H:i:s')) {
                    $this->line($customer->name.' id: '.$customer->id);
                    $this->line('max order: '.$max_order_date);
                    $deliveries = $delivery_service->deliveries()->where('date', '>=', now()->format('Y-m-d'))->where('date', '<=', $max_order_date)->get();
                    foreach ($deliveries as $delivery) {
                        $order = $delivery->orders()->where('customer_id', $customer->id)->first();
                        if (is_null($order)) {
                            $fixes = true;
                            $this->line('<bg=red>keine Lieferung für '.$delivery->date->format('d.m.Y').' geplant!</>');
                            Order::create([
                                'delivery_id' => $delivery->id,
                                'customer_id' => $customer->id,
                                'product_id' => $max_order->product_id,
                            ]);

                            $credit_of_product = $customer->creditOfProduct($max_order->product, true, false);
                            $this->info('credit: '.$credit_of_product);
                            if($credit_of_product < 0) {
                                do {
                                    $unaffordable_order = $customer->orders()->whereHas('delivery', function ($q) use ($max_order_date, $delivery) {
                                        $q->where('date', '<=', $max_order_date)->where('date', '>=', $delivery->date);
                                    })->where('affordable', '=', 1)->get()->sortByDesc(function ($order, $key) {
                                        return $order->delivery->date;
                                    })->first();

                                    if(! is_null($unaffordable_order)) {
                                        $this->info('unafordable date: '.$unaffordable_order->delivery->date->format('Y-m-d'));
                                        $unaffordable_order->update(['affordable' => 0]);
                                        $credit_of_product = $customer->creditOfProduct($max_order->product, true, false);
                                        $this->info('credit: '.$credit_of_product);
                                    }
                                } while($credit_of_product < 0 && ! is_null($unaffordable_order));
                            }
                        }
                    }
                }
            }

            if($fixes) {
                $admins = User::role('admin')->get();
                foreach($admins as $admin) {
                    $admin->notify(new FixOrdersNotification($customer));
                }
            }
        }

        return Command::SUCCESS;
    }
}
