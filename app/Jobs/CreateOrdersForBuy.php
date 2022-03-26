<?php

namespace App\Jobs;

use App\Models\Buy;
use App\Models\Delivery;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateOrdersForBuy implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Buy $buy;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Buy $buy)
    {
        $this->buy = $buy;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // richtiger Delivery Service finden
    }
}
