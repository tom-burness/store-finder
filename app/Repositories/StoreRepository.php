<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DataTransferObjects\PostcodeData;
use App\Http\Requests\StoreCreateRequest;
use App\Models\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

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

    public function getPaginatedByPostcode(PostcodeData $postcodeData)
    {
        return $this->selectQuery($postcodeData->lat, $postcodeData->long)
            ->orderBy('distance')
            ->paginate(10);
    }

    public function getPaginatedDeliverableByPostcode(PostcodeData $postcodeData)
    {
        return $this->selectQuery($postcodeData->lat, $postcodeData->long)
            ->having('distance', '<=', DB::raw('max_delivery_distance'))
            ->orderBy('distance')
            ->paginate(10);
    }

    private function selectQuery(float $lat, float $long): Builder
    {
        return Store::select(
            'name',
            'status',
            'type',
            'max_delivery_distance',
            DB::raw("ST_Distance_Sphere(coordinates, ST_GeomFromText('POINT($lat $long)', 4326)) / 1000 AS distance")
        );
    }
}
