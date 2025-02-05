<?php

namespace App\Jobs;

use App\DataImport\ReadFileChunks;
use App\DataTransferObjects\PostcodeData;
use App\Models\Postcode;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

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
    public function handle(ReadFileChunks $chunkReader): void
    {
        info("Processing file", [
            'file' => $this->filepath,
        ]);

        /**
         * NOTE: This could cause issues and it would need to be considered the impact it would have on the FE/API
         * To keep the DB clean I have just wiped it all each time data is imported. INSERT UPDATE could be an option
         * but I think there would be a performance impact on that
         */
        Postcode::truncate();

        /**
         * NOTE: when processing the file, we want to insert all the data within a transaction.
         * The reason for this is that we dont want only some data from the file inserted before an error
         * If an error occurs, we can retry the file. */
        DB::transaction(function () use ($chunkReader) {
            foreach ($chunkReader->iterate($this->filepath) as $postcodeChunk) {
                $insertData = $postcodeChunk->map(fn (PostcodeData $data) => [
                    'postcode' => $data->pcd,
                    'coordinates' => DB::raw("ST_GeomFromText('POINT($data->lat $data->long)', 4326)"), // lat: 51..., long: -0.1
                    'long' => $data->long,
                    'lat' => $data->lat,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                info("Inserting batch", [
                    'file' => $this->filepath,
                    'count' => $insertData->count()
                ]);
                Postcode::insert($insertData->toArray());
                info("Inserted", [
                    'file' => $this->filepath,
                    'count' => $insertData->count()
                ]);
            }
        });
    }
}
