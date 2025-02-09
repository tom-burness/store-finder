<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Http\Requests\StoreCreateRequest;
use App\Models\Store;
use Illuminate\Support\Facades\DB;

class StoreRepository
{
    public function __construct(
        protected Store $store
    ) {

    }

    public function createFromStoreRequest(StoreCreateRequest $request): ?Store
    {
        $lat = (float) $request->latitude;
        $long = (float) $request->longitude;
        return Store::create([
            'name' => $request->name,
            'status' => $request->status,
            'type' => $request->type,
            'long' => $long,
            'lat' => $lat,
            'coordinates' => DB::raw("ST_GeomFromText('POINT($lat $long)', 4326)"),
            'max_delivery_distance' => $request->max_delivery_distance
        ]);
    }
}
