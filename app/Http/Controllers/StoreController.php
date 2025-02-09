<?php

namespace App\Http\Controllers;

use App\Actions\CreateStoreFromRequest;
use App\Actions\GetStoresFromRequest;
use App\Http\Requests\StoreCreateRequest;
use App\Http\Requests\StoreIndexRequest;
use App\Http\Resources\StoreCollection;
use App\Http\Resources\StoreResource;
use Illuminate\Http\JsonResponse;

class StoreController extends Controller
{
    public function __construct(
        protected GetStoresFromRequest $getStoresFromRequestAction,
        protected CreateStoreFromRequest $createStoreFromRequestAction
    ) {

    }
    /**
     * Display a listing of the resource.
     *
     * @return StoreCollection<int, StoreResource>
     */
    public function index(StoreIndexRequest $request): StoreCollection|JsonResponse
    {
        return $this->getStoresFromRequestAction->handle($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCreateRequest $request): JsonResponse
    {
        return $this->createStoreFromRequestAction->handle($request);

    }
}
