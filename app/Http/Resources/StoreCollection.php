<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

/**
 * @mixin \Illuminate\Support\Collection
 */
class StoreCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>|Arrayable|JsonSerializable
     */
    public function toArray(Request $request): array|Arrayable|JsonSerializable
    {
        return parent::toArray($request);
    }
}
