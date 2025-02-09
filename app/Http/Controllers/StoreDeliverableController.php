<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\PostcodeData;
use App\Http\Requests\StoreIndexRequest;
use App\Http\Resources\StoreCollection;
use App\Http\Resources\StoreResource;
use App\Models\Postcode;
use App\Models\Store;
use App\Repositories\PostcodeRepository;
use App\Repositories\StoreRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StoreDeliverableController extends Controller
{
    public function __construct(
        protected PostcodeRepository $postcodeRepository,
        protected StoreRepository $storeRepository
    ) {

    }

    /**
     * Handle the incoming request.
     *
     * @return StoreCollection<int, StoreResource>
     */
    public function __invoke(StoreIndexRequest $request): StoreCollection|JsonResponse
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

        $stores = $this->storeRepository->getPaginatedDeliverableByPostcode($postcodeData);

        return new StoreCollection($stores);
    }
}
