<?php

namespace App\Http\Controllers;

use App\Actions\GetStoresFromRequest;
use App\Http\Requests\StoreIndexRequest;
use App\Http\Resources\StoreCollection;
use App\Http\Resources\StoreResource;
use Illuminate\Http\JsonResponse;

class StoreDeliverableController extends Controller
{
    public function __construct(
        protected GetStoresFromRequest $getStoresFromRequestAction,
    ) {

    }

    /**
     * Handle the incoming request.
     *
     * @return StoreCollection<int, StoreResource>
     */
    public function __invoke(StoreIndexRequest $request): StoreCollection|JsonResponse
    {
        return $this->getStoresFromRequestAction->handle($request, true);
    }
}
