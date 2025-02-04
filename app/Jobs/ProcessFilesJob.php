<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class ProcessFilesJob implements ShouldQueue
{
    use Batchable;
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        readonly protected string $extractedDirectoryName = "extractedPostcodeFiles"
    ) {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (! $this->batch() || $this->batch()->cancelled()) {
            return;
        }

        info("Process files");

        $path = Storage::disk('local')->path('');
        $files = collect(Storage::disk('local')->files("$this->extractedDirectoryName/Data/multi_csv"));

        foreach ($files as $filePath) {
            info("Add file to batch");
            $this->batch()->add(
                new ImportCsvJob($path . $filePath)
            );
        }
    }
}
