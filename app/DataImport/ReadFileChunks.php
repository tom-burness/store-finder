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
            yield collect($chunk)->map(
                fn ($data) => new PostcodeData(
                    $data['pcd'],
                    (float) $data['lat'],
                    (float) $data['long']
                )
            );
        }
    }
}
