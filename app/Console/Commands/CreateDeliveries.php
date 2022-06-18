<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateDeliveries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deliveries:create';

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
        return \App\Jobs\CreateDeliveries::dispatchSync();
    }
}
