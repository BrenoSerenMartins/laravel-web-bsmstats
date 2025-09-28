<?php

namespace App\Console\Commands;

use App\Jobs\SyncChampionsJob;
use Illuminate\Console\Command;

class FetchChampionsData extends Command
{
    protected $signature = 'app:fetch-champions-data';
    protected $description = 'Dispatches a job to fetch champion data from Riot\'s Data Dragon';

    public function handle()
    {
        $this->info('Dispatching job to sync champion data...');
        SyncChampionsJob::dispatch();
        $this->info('Job dispatched successfully!');
        return 0;
    }
}
