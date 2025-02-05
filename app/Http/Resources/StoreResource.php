<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Store
 * @property ?int $distance
 */
class StoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'status' => $this->status,
            'type' => $this->type,
            'distance' => $this->whenHas('distance', round($this->distance))
        ];
    }
}
