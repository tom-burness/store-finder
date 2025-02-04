<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

final readonly class PostcodeData
{
    public function __construct(
        public string $pcd,
        public float $lat,
        public float $long
    ) {

    }
}
