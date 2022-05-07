<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;

class BackupCleaner extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes old Backups';

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

        // list databases
        $database = env('DB_DATABASE');
        $path = storage_path('db_backup/'.$database);

        $files = scandir($path);

        foreach (array_diff($files, ['.', '..']) as $file) {
            $parts = explode('__', $file);
            $date = substr($parts[1], 0, 16);
            $date_arr = explode('_', $date);
            $time_arr = explode('-', $date_arr[1]);
            $carbon = Carbon::parse($date_arr[0].' '.$time_arr[0].':'.$time_arr[1]);

            if ($carbon->diffInDays() > 14) {
                unlink($path.'/'.$file);
            }
        }

        $this->info('command finished');

        return 0;
    }
}
