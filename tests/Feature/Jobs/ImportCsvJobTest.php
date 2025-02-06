<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs;

use App\DataImport\ReadFileChunks;
use App\Jobs\ImportCsvJob;
use App\Models\Postcode;
use App\Repositories\PostcodeRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportCsvJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_job_imports_all_valid_data_from_csv(): void
    {
        // Set to prove the job truncates data first
        Postcode::factory(1)->create([
            'postcode' => 'TE55 TNG'
        ]);

        $filePath = base_path('tests/Support/postcodesWithInvalidData.csv');

        $reader = new ReadFileChunks();

        $sut = new ImportCsvJob($filePath);
        $sut->handle(
            app(PostcodeRepository::class),
            $reader
        );

        $this->assertDatabaseCount('postcodes', 13);
    }
}
