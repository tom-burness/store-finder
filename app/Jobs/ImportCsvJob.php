<?php

namespace App\Jobs;

use App\DataImport\ReadFileChunks;
use App\Repositories\PostcodeRepository;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ImportCsvJob implements ShouldQueue
{
    use Batchable;
    use Queueable;

    public int $timeout = 120;
    public int $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly string $filepath
    ) {

    }

    /**
     * Execute the job.
     */
    public function handle(
        PostcodeRepository $postcodeRepository,
        ReadFileChunks $chunkReader
    ): void {
        info("Processing file", [
            'file' => $this->filepath,
        ]);

        $postcodeRepository->insertBatchedData(
            $chunkReader->iterate($this->filepath)
        );
    }
}
