<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\BatchStatusEnum;
use App\Exceptions\BatchException;
use App\Http\Controllers\Controller;
use App\Http\Requests\SendBatchRequest;
use App\Http\Requests\ShowBatchRequest;
use App\Libraries\Response;
use App\Repositories\BatchRepository;
use App\Repositories\PurchaseRepository;
use App\Libraries\BatchAPIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Throwable;

class BatchController extends Controller
{
    /**
     * Batch's repository
     *
     * @var BatchRepository
     */
    protected BatchRepository $repo;

    /**
     * Purchase's repository
     *
     * @var PurchaseRepository
     */
    protected PurchaseRepository $purchaseRepo;

    /**
     * BatchController constructor.
     * @param BatchRepository $repository
     * @param PurchaseRepository $purchaseRepo
     */
    public function __construct(BatchRepository $repository, PurchaseRepository $purchaseRepo)
    {
        $this->repo = $repository;
        $this->purchaseRepo = $purchaseRepo;
    }

    /**
     * Sending batch CSV file.
     *
     * @param SendBatchRequest $request
     * @return JsonResponse
     * @throws BatchException
     */
    public function store(SendBatchRequest $request): JsonResponse
    {
        try {
            $file = Arr::get($request->allFiles(), 'file');
            $payload = [
                'status' => BatchStatusEnum::IN_FILE,
                'path' => $file->store('public/files'),
                #'filename' => $file->getClientOriginalName(),
            ];

            DB::beginTransaction();
            $batch = $this->repo->store($payload);
            DB::commit();

            $data = [
                'type' => get_class($batch),
                'uuid' => $batch->uuid,
                'links' => [
                    'self' => route('show', ['uuid' => $batch->uuid])
                ]
            ];

            return Response::json(201, $data);
        } catch (Throwable $e) {
            DB::rollBack();
            throw new BatchException($e);
        }
    }

    /**
     * @param ShowBatchRequest $request
     * @param string $uuid
     * @return JsonResponse
     * @throws BatchException
     */
    public function show(ShowBatchRequest $request, string $uuid): JsonResponse
    {
        try {
            $batch = $this->repo->show($uuid);

            $data = [
                'uuid' => $uuid,
                'status' => $batch->status->description
            ];

            if ($batch->status->is(BatchStatusEnum::IN_FILE)) {
                return Response::json(200, $data, 'Batch has not yet been uploaded.');
            } else if ($batch->status->is(BatchStatusEnum::ERROR)) {
                return Response::json(200, $data, 'Could not load batch.', $batch->errors);
            } else if ($batch->status->is(BatchStatusEnum::LOADING)) {
                return Response::json(200, $data, 'Batch is loading, please try later.');
            }

            $query = $this->purchaseRepo->show(['batch_uuid' => $batch->uuid]);
            $params = $request->all();
            $data = $data + BatchAPIService::dataQuery($query, $params);

            return Response::json(200, $data, null, null, Arr::has($params, 'pretty'));
        } catch (Throwable $e) {
            DB::rollBack();
            throw new BatchException($e);
        }
    }

}
