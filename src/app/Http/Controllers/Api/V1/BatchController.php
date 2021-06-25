<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\BatchStatusEnum;
use App\Exceptions\PurchaseException;
use App\Http\Controllers\Controller;
use App\Http\Requests\SendBatchRequest;
use App\Http\Requests\ShowBatchRequest;
use App\Jobs\LoadBatchFilesJob;
use App\Models\Batch;
use App\Repositories\BaseRepository;
use App\Repositories\BatchRepository;
use App\Repositories\PurchaseRepository;
use App\Services\BatchAPIService;
use App\Services\FileService;
use App\Services\UploadService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Arr;
use Phpro\ApiProblem\Exception\ApiProblemException;
use Phpro\ApiProblem\Http\ForbiddenProblem;
use Phpro\ApiProblem\Http\PreconditionRequiredProblem;
use Prophecy\Exception\Doubler\MethodNotFoundException;
use Ramsey\Uuid\Uuid;
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
     * @throws PurchaseException
     */
    public function store(SendBatchRequest $request): JsonResponse
    {
        try {
            $file = Arr::get($request->allFiles(), 'file');

            $batch = $this->repo->store([
                'status' => BatchStatusEnum::IN_FILE,
                'path' => $file->store('public/files'),
                #'filename' => $file->getClientOriginalName(),
            ]);

            #dispatch((new LoadBatchFilesJob()));

            return response()->json([
                'success' => true,
                'uuid' => $batch->uuid,
                'url' => route('show', ['uuid' => $batch->uuid])
            ]);
        } catch (Throwable $e) {
            DB::rollBack();
            throw new PurchaseException($e);
        }
    }

    /**
     * @param ShowBatchRequest $request
     * @param string $uuid
     * @return JsonResponse
     * @throws PurchaseException
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
                return response()->json($data + ['message' => 'Batch has not yet been uploaded.'], 200);
            } else if ($batch->status->is(BatchStatusEnum::ERROR)) {
                return response()->json($data + ['message' => 'Could not load batch.', 'errors' => $batch->errors], 200);
            } else if ($batch->status->is(BatchStatusEnum::LOADING)) {
                return response()->json($data + ['message' => 'Batch is loading, please try later.'], 200);
            }

            $query = $this->purchaseRepo->show(['batch_uuid' => $batch->uuid]);

            $data = $data + BatchAPIService::dataQuery($query, $request->all());

            return response()->json($data, 200, [], 0);
        } catch (Throwable $e) {
            DB::rollBack();
            throw new PurchaseException($e);
        }
    }

}
