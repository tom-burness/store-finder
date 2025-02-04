<?php

declare(strict_types=1);

namespace App\DataImport\Actions;

use Illuminate\Support\Facades\Storage;

final class DownloadFileFromUrl
{
    public function handle(
        string $sourceUrl,
        string $destinationFileName
    ): bool {
        $localDisk = Storage::disk('local');

        if ($localDisk->exists($destinationFileName)) {
            info("File already exists, skipping");
            return true;
        }

        info("Downloading File");
        $contents = file_get_contents($sourceUrl);

        if (! $contents) {
            return false;
        }

        return (bool) $localDisk->put($destinationFileName, $contents);
    }
}
