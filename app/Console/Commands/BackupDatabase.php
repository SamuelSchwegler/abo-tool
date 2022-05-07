<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\DbDumper\Compressors\GzipCompressor;
use Spatie\DbDumper\Databases\MySql;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dumps Database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('command started');

        $time = date('Y-m-d_H-i');

        // list databases
        $database = env('DB_DATABASE');

        // make db_backup folder if not existing
        if (! file_exists(storage_path('db_backup'))) {
            mkdir(storage_path('db_backup'));
        }

        // create folders
        $path = storage_path('db_backup/'.$database);
        if (! file_exists($path)) {
            mkdir($path);
        }

        // zip data
        MySql::create()
            ->setDbName($database)
            ->setUserName(env('DB_USERNAME'))
            ->setPassword(env('DB_PASSWORD'))
            ->setHost('localhost')
            ->addExtraOption('-u '.env('DB_USERNAME', 'forge'))
            ->useCompressor(new GzipCompressor())
            ->dumpToFile($path.'/'.$database.'__'.$time.'.sql.gz');

        $this->info('dump for '.$database.' created');

        $this->info('command finished');

        return 0;
    }
}
