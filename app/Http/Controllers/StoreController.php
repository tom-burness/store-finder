<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\PostcodeData;
use App\Http\Requests\StoreCreateRequest;
use App\Http\Requests\StoreIndexRequest;
use App\Http\Resources\StoreCollection;
use App\Http\Resources\StoreResource;
use App\Repositories\PostcodeRepository;
use App\Repositories\StoreRepository;
use Illuminate\Http\JsonResponse;

class StoreController extends Controller
{
    public function __construct(
        protected PostcodeRepository $postcodeRepository,
        protected StoreRepository $storeRepository
    ) {

    }
    /**
     * Display a listing of the resource.
     *
     * @return StoreCollection<int, StoreResource>
     */
    public function index(StoreIndexRequest $request): StoreCollection|JsonResponse
    {
        $postcodeInput = $request->query('postcode');

        $postcodeRecord = $this->postcodeRepository->findByPostcode($postcodeInput);

        if (! $postcodeRecord) {
            return response()->json(['error' => 'We cannot locate that postcode'], 404);
        }

        $postcodeData = new PostcodeData(
            $postcodeRecord->postcode,
            $postcodeRecord->lat,
            $postcodeRecord->long
        );

        $stores = $this->storeRepository->getPaginatedByPostcode($postcodeData);

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

        $store = $this->storeRepository->createFromStoreRequest($request);

        return (new StoreResource($store))
            ->response()
            ->setStatusCode(201);
    }
}
