<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DataTransferObjects\PostcodeData;
use App\Models\Postcode;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PostcodeRepository
{
    public function __construct(
        protected Postcode $postcode
    ) {

    }

    public function findByPostcode(string $postcode): ?Postcode
    {
        return $this->postcode
            ->newQuery()
            ->where('postcode', $postcode)
            ->first();
    }

    /**
     * @param iterable<Collection<int,PostcodeData>> $chunkedPostcodeData
     */
    public function insertBatchedData(iterable $chunkedPostcodeData): void
    {
        /**
         * NOTE: This could cause issues and it would need to be considered the impact it would have on the FE/API
         * To keep the DB clean I have just wiped it all each time data is imported. INSERT UPDATE could be an option
         * but I think there would be a performance impact on that
         */
        $this->postcode
            ->newInstance()
            ->truncate();

        /**
         * NOTE: when processing the file, we want to insert all the data within a transaction.
         * The reason for this is that we dont want only some data from the file inserted before an error
         * If an error occurs, we can retry the file. */
        $this->postcode
            ->getConnection()
            ->transaction(function () use ($chunkedPostcodeData) {
                foreach ($chunkedPostcodeData as $postcodeChunk) {
                    $insertData = $postcodeChunk->map(fn (PostcodeData $data) => [
                        'postcode' => $data->pcd,
                        'coordinates' => DB::raw("ST_GeomFromText('POINT($data->lat $data->long)', 4326)"), // lat: 51..., long: -0.1
                        'long' => $data->long,
                        'lat' => $data->lat,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);

                    Postcode::insert($insertData->toArray());
                }
            });
    }
}
