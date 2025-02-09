<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCreateRequest;
use App\Http\Requests\StoreIndexRequest;
use App\Http\Resources\StoreCollection;
use App\Http\Resources\StoreResource;
use App\Models\Postcode;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return StoreCollection<int, StoreResource>
     */
    public function index(StoreIndexRequest $request): StoreCollection|JsonResponse
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
            ->orderBy('distance')
            ->paginate(10);

        return new StoreCollection($stores);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCreateRequest $request): JsonResponse
    {
        /**
         * NOTE: Policies are better but given this is the only
         * endpoint which I am protecting with a user ability, its OK for now
         */

        if (! $request->user()?->tokenCan("createStore")) {
            return response()->json([
                'message' => 'You cannot create a store.',
            ], 403);
        }

        /**
         * NOTE: Improvement. Allow postcode to be POSTed and use the postcode table to get the coordinates
         */

        $lat = (float) $request->latitude;
        $long = (float) $request->longitude;
        $store = Store::create([
            'name' => $request->name,
            'status' => $request->status,
            'type' => $request->type,
            'long' => $long,
            'lat' => $lat,
            'coordinates' => DB::raw("ST_GeomFromText('POINT($lat $long)', 4326)"),
            'max_delivery_distance' => $request->max_delivery_distance
        ]);

        return (new StoreResource($store))
            ->response()
            ->setStatusCode(201);
    }
}
