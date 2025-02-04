<?php

declare(strict_types=1);

namespace App\DataImport\Actions;

use Illuminate\Support\Facades\Storage;
use ZipArchive;

final class ExtractFile
{
    public function handle(
        string $sourceFileName,
        string $extractedDirectoryName
    ): bool {
        $localDisk = Storage::disk('local');

        if (! $localDisk->exists($sourceFileName)) {
            return false;
        }

        $zip = new ZipArchive();
        $zipFile = $zip->open($localDisk->path($sourceFileName));

        if (! $zipFile) {
            return false;
        }

        // NOTE: I would like this to only extract the files we need
        info("Extracting File");
        $zip->extractTo(Storage::disk('local')->path($extractedDirectoryName));

        return $zip->close();
    }
}
