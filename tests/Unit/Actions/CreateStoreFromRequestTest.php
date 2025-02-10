<?php

namespace Tests\Unit\Actions;

use App\Actions\CreateStoreFromRequest;
use App\Http\Requests\StoreCreateRequest;
use App\Http\Resources\StoreResource;
use App\Models\Store;
use App\Repositories\PostcodeRepository;
use App\Repositories\StoreRepository;
use Illuminate\Http\JsonResponse;
use Mockery;
use PHPUnit\Framework\TestCase;

class CreateStoreFromRequestTest extends TestCase
{
    public function test_handle_returns_403_when_user_cannot_create_store()
    {
        $postcodeRepository = Mockery::mock(PostcodeRepository::class);
        $storeRepository = Mockery::mock(StoreRepository::class);

        $request = Mockery::mock(StoreCreateRequest::class);
        $request->shouldReceive('user->tokenCan')
            ->with('createStore')
            ->andReturn(false);

        /** @var PostcodeRepository $postcodeRepository */
        /** @var StoreRepository $storeRepository */
        $action = new CreateStoreFromRequest($postcodeRepository, $storeRepository);

        /** @var StoreCreateRequest $request */
        $response = $action->handle($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertEquals([
            'message' => 'You cannot create a store.'
        ], $response->getData(true));
    }

    public function test_handle_creates_store_successfully()
    {
        $postcodeRepository = Mockery::mock(PostcodeRepository::class);
        $storeRepository = Mockery::mock(StoreRepository::class);

        $request = Mockery::mock(StoreCreateRequest::class);
        $request->shouldReceive('user->tokenCan')
            ->with('createStore')
            ->andReturn(true);

        $store = Mockery::mock(Store::class);
        $storeRepository->shouldReceive('createFromStoreRequest')
            ->with($request)
            ->andReturn($store);

        /** @var PostcodeRepository $postcodeRepository */
        /** @var StoreRepository $storeRepository */
        $action = new CreateStoreFromRequest($postcodeRepository, $storeRepository);

        /** @var StoreCreateRequest $request */
        $response = $action->handle($request);

        $this->assertInstanceOf(StoreResource::class, $response);
    }
}
