<?php

namespace App\Jobs;

use App\DataImport\Actions\DownloadFileFromUrl;
use App\DataImport\Actions\ExtractFile;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DownloadFileJob implements ShouldQueue
{
    use Queueable;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 600;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        readonly protected string $fileName = 'postcodeFileDownload.zip',
        readonly protected string $extractedDirectoryName = 'extractedPostcodeFiles'
    ) {

    }

    /**
     * Execute the job.
     */
    public function handle(
        DownloadFileFromUrl $downloadFileAction,
        ExtractFile $extractFileAction
    ): void {
        /**
         * NOTE: This file must be trusted and the structure of the zip must not change.
         * We are trusting this file is safe. Given more time I would want to look into
         * the security of this.
         */
        $url = "https://parlvid.mysociety.org/os/ONSPD/2022-11.zip";

        if (! $downloadFileAction->handle($url, $this->fileName)) {
            return;
        }

        $extractFileAction->handle(
            $this->fileName,
            $this->extractedDirectoryName
        );
    }
}
