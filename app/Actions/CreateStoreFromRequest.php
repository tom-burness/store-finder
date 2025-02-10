<?php

declare(strict_types=1);

namespace App\Actions;

use App\Http\Requests\StoreCreateRequest;
use App\Http\Resources\StoreResource;
use App\Repositories\PostcodeRepository;
use App\Repositories\StoreRepository;
use Illuminate\Http\JsonResponse;

final class CreateStoreFromRequest
{
    public function __construct(
        protected PostcodeRepository $postcodeRepository,
        protected StoreRepository $storeRepository
    ) {

    }

    public function handle(StoreCreateRequest $request): JsonResponse|StoreResource
    {
        /**
         * NOTE: Policies are better but given this is the only
         * endpoint which I am protecting with a user ability, its OK for now
         */

        if (! $request->user()?->tokenCan("createStore")) {
            return new JsonResponse([
                'message' => 'You cannot create a store.',
            ], 403);
        }

        /**
         * NOTE: Improvement. Allow postcode to be POSTed and use the postcode table to get the coordinates
         */

        $store = $this->storeRepository->createFromStoreRequest($request);

        return new StoreResource($store);
    }
}
