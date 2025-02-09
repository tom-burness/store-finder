<?php

declare(strict_types=1);

namespace App\Actions;

use App\DataTransferObjects\PostcodeData;
use App\Http\Requests\StoreIndexRequest;
use App\Http\Resources\StoreCollection;
use App\Repositories\PostcodeRepository;
use App\Repositories\StoreRepository;
use Illuminate\Http\JsonResponse;

final class GetStoresFromRequest
{
    public function __construct(
        protected PostcodeRepository $postcodeRepository,
        protected StoreRepository $storeRepository
    ) {

    }

    public function handle(
        StoreIndexRequest $request,
        bool $deliverable = false
    ): StoreCollection|JsonResponse {
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

        /**
         * This could be refactored into a strategy pattern. Since it is a simple bool check
         * it is OK to leave it like this for now.
         */
        if ($deliverable) {
            $stores = $this->storeRepository->getPaginatedDeliverableByPostcode($postcodeData);
        } else {
            $stores = $this->storeRepository->getPaginatedByPostcode($postcodeData);
        }

        return new StoreCollection($stores);
    }
}
