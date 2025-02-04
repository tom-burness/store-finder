<?php

namespace App\Console\Commands;

use App\Jobs\DownloadFileJob;
use App\Jobs\ProcessFilesJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class ImportPostcodeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-postcode 
                            {--skip-download : Skip downloading, useful if the files already exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download, and import postcode data';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $chain = [];
        if (! $this->option('skip-download')) {
            $chain[] = new DownloadFileJob();
        }

        $chain[] = Bus::batch([
            new ProcessFilesJob()
        ])->name('Process Postcode Files');

        Bus::chain($chain)->dispatch();
    }
}
