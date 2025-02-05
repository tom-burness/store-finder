<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIndexRequest;
use App\Http\Resources\StoreCollection;
use App\Http\Resources\StoreResource;
use App\Models\Postcode;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreDeliverableController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return StoreCollection<int, StoreResource>
     */
    public function __invoke(StoreIndexRequest $request): StoreCollection|JsonResponse
    {
        $postcodeInput = $request->query('postcode');

        $postcodeRecord = Postcode::where('postcode', $postcodeInput)->first();

        if (! $postcodeRecord) {
            return response()->json(['error' => 'We cannot locate that postcode'], 404);
        }

        $lat = $postcodeRecord->lat;
        $long = $postcodeRecord->long;

        $stores = Store::select(
            'name',
            'status',
            'type',
            'max_delivery_distance',
            DB::raw("ST_Distance_Sphere(coordinates, ST_GeomFromText('POINT($lat $long)', 4326)) / 1000 AS distance")
        )
            ->having('distance', '<=', DB::raw('max_delivery_distance'))
            ->orderBy('distance')
            ->paginate(10);

        return new StoreCollection($stores);
    }
}
