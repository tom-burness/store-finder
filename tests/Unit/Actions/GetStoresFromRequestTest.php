<?php

namespace Tests\Unit\Actions;

use App\Actions\GetStoresFromRequest;
use App\Http\Requests\StoreIndexRequest;
use App\Http\Resources\StoreCollection;
use App\Models\Postcode;
use App\Repositories\PostcodeRepository;
use App\Repositories\StoreRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
use PHPUnit\Framework\TestCase;

class GetStoresFromRequestTest extends TestCase
{
    public function test_handle_returns_error_response_when_postcode_not_found()
    {
        $postcodeRepository = Mockery::mock(PostcodeRepository::class);
        $storeRepository = Mockery::mock(StoreRepository::class);

        $postcodeRepository->shouldReceive('findByPostcode')
            ->andReturn(null);

        $request = Mockery::mock(StoreIndexRequest::class);
        $request->shouldReceive('query')
            ->with('postcode')
            ->andReturn('SE9 9AF');

        /** @var PostcodeRepository $postcodeRepository */
        /** @var StoreRepository $storeRepository */
        $action = new GetStoresFromRequest($postcodeRepository, $storeRepository);

        /** @var StoreIndexRequest $request */
        $response = $action->handle($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals([
            'error' => 'We cannot locate that postcode'
        ], $response->getData(true));
    }

    public function test_handle_returns_store_collection_when_postcode_is_found()
    {
        $postcodeRepository = Mockery::mock(PostcodeRepository::class);
        $storeRepository = Mockery::mock(StoreRepository::class);

        $postcode = new Postcode([
            'postcode' => 'SE9 9AF',
            'lat' => 51.449793,
            'long' => 0.05268
        ]);
        $postcodeRepository->shouldReceive('findByPostcode')
            ->with('SE9 9AF')
            ->andReturn($postcode);

        $paginator = new LengthAwarePaginator([], 0, 10);
        $storeRepository->shouldReceive('getPaginatedByPostcode')
            ->andReturn($paginator);

        $request = Mockery::mock(StoreIndexRequest::class);
        $request->shouldReceive('query')
            ->with('postcode')
            ->andReturn('SE9 9AF');

        /** @var PostcodeRepository $postcodeRepository */
        /** @var StoreRepository $storeRepository */
        $action = new GetStoresFromRequest($postcodeRepository, $storeRepository);

        /** @var StoreIndexRequest $request */
        $response = $action->handle($request);

        $this->assertInstanceOf(StoreCollection::class, $response);
    }

    public function test_handle_returns_store_collection_for_deliverable()
    {
        $postcodeRepository = Mockery::mock(PostcodeRepository::class);
        $storeRepository = Mockery::mock(StoreRepository::class);

        $postcode = new Postcode([
            'postcode' => 'SE9 9AF',
            'lat' => 51.449793,
            'long' => 0.05268
        ]);
        $postcodeRepository->shouldReceive('findByPostcode')
            ->with('SE9 9AF')
            ->andReturn($postcode);

        $paginator = new LengthAwarePaginator([], 0, 10);
        $storeRepository->shouldReceive('getPaginatedDeliverableByPostcode')
            ->andReturn($paginator);

        $request = Mockery::mock(StoreIndexRequest::class);
        $request->shouldReceive('query')
            ->with('postcode')
            ->andReturn('SE9 9AF');

        /** @var PostcodeRepository $postcodeRepository */
        /** @var StoreRepository $storeRepository */
        $action = new GetStoresFromRequest($postcodeRepository, $storeRepository);

        /** @var StoreIndexRequest $request */
        $response = $action->handle($request, true);

        $this->assertInstanceOf(StoreCollection::class, $response);
    }
}
