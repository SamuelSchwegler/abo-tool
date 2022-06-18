<?php

namespace App\Jobs;

use App\Models\Buy;
use App\Models\Customer;
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
        CreateOrders::dispatch($this->customer, $this->buy->bundle->product, $this->from);
    }
}
