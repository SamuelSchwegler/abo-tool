<?php

namespace App\Console\Commands;

use App\Models\Bundle;
use App\Models\Delivery;
use App\Models\DeliveryService;
use App\Models\Item;
use App\Models\ItemOrigin;
use App\Models\Postcode;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CopyProdSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'copy:prod-settings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected array $models_to_copy = [DeliveryService::class, Postcode::class, Product::class, Bundle::class,
        Delivery::class, ItemOrigin::class, Item::class];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (env('DB_DATABASE_COPY', false)) {
            // check if same have same version
            $latest = DB::connection('mysql')->table('migrations')->orderByDesc('id')->first()->migration;
            $hasRun = DB::connection('mysql-copy')->table('migrations')->where('migration', $latest)->exists();

            if ($hasRun) {
                $this->info($latest . ' has run');
                $this->info('start with copy');

                foreach ($this->models_to_copy as $model) {
                    $this->info('kopieren von ' . $model);
                    DB::connection('mysql')->table((new $model)->getTable())->truncate();
                    foreach ((new $model)->on('mysql-copy')->get() as $service) {
                        $this->copyModel($service);
                    }
                }
            } else {
                $this->info('dbs are not equal, migration: ' . $latest . ' does not exists');
            }
        } else {
            $this->info('no copy db defined');
        }

        $this->info('finito');
    }

    public function copyModel($model)
    {
        $newModel = $model
            ->replicate()
            ->setConnection('mysql')
            ->setTable($model->getTable());

        DB::transaction(function () use ($newModel) {
            $newModel->save();
        });
    }
}
