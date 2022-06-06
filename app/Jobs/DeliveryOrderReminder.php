<?php

namespace App\Jobs;

use App\Models\Delivery;
use App\Notifications\OrderReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DeliveryOrderReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $due_deliveries = Delivery::deadlineOnNextDay();

        foreach ($due_deliveries as $delivery) {
            foreach ($delivery->orders as $order) {
                if (!$order->reminded && !$order->canceled) {
                    Log::info($order->customer);
                    $order->customer?->user?->notify(new OrderReminder($order));
                }
            }
        }
    }
}
