<?php

declare(strict_types=1);

namespace App\DataImport;

use App\DataTransferObjects\PostcodeData;
use Illuminate\Support\Collection;
use League\Csv\Reader;

final class ReadFileChunks
{
    // NOTE: Split file up into chunks to prevent loading too many rows into memory.
    public const CHUNK_SIZE = 10000;

    /**
     * @return iterable<Collection<int,PostcodeData>>
     */
    public function iterate(string $filePath): iterable
    {
        $chunks = Reader::createFromPath($filePath)
            ->setHeaderOffset(0)
            ->select('pcd', 'lat', 'long')
            ->chunkBy(self::CHUNK_SIZE);

        foreach ($chunks as $chunk) {
            yield collect($chunk)->map(function ($data) {
                /**
                 * NOTE: I am validating the lat long here but I would put this within a value object which ensures
                 * it can only be created if it has valid data
                 */
                $lat = (float) $data['lat'];
                $long = (float) $data['long'];

                if (
                    abs($lat) > 90 ||
                    abs($long) > 180
                ) {
                    return null;
                }

                return new PostcodeData(
                    $data['pcd'],
                    $lat,
                    $long
                );
            })->filter();
        }
    }
}
