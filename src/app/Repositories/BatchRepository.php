<?php

namespace App\Repositories;

use App\Models\Batch;
use Illuminate\Support\Facades\DB;

class BatchRepository extends BaseRepository
{
    /**
     * @var string
     */
    protected $model = Batch::class;

    /**
     * @var PurchaseRepository
     */
    protected $purchaseRepo;

    /**
     * BatchRepository constructor.
     */
    public function __construct()
    {
        $this->purchaseRepo = (new PurchaseRepository);
    }

    /**
     * Save batch in db.
     *
     * @param array $payload
     * @return mixed
     */
    public function store(array $payload, Batch $model = null)
    {
        DB::beginTransaction();

        $obj = $model ?? (new $this->model);
        $obj->fill($payload)->save();

        DB::commit();

        return $obj;
    }

    /**
     * Show batch by UUID
     *
     * @param string $uuid
     * @return Batch
     */
    public function show(string $uuid): Batch
    {
        return Batch::findOrFail($uuid);
    }

//    /**
//     * Loading batche's files with status IN_FILE and save in database's tables.
//     * @throws \BenSampo\Enum\Exceptions\InvalidEnumKeyException
//     */
//    public function loading()
//    {
//        $batches = $this->model::where('status', BatchStatusEnum::IN_FILE)->get();
//
//        DB::beginTransaction();
//
//        foreach ($batches as $batch) {
//            $batch->status = BatchStatusEnum::LOADING;
//            $batch->save();
//
//            $csv = BatchCSVService::readerByPath($batch->path);
//
//            $feedback = $this->purchaseRepo->saveAll($csv->getRecords(), $batch->uuid);
//
//            if (Arr::has($feedback, 'success')) {
//                $batch->ready_at = Carbon::now();
//                $batch->status = BatchStatusEnum::DONE;
//            } else {
//                $batch->errors = Arr::get($feedback, 'errors');
//                $batch->status = BatchStatusEnum::ERROR;
//            }
//
//            $batch->save();
//        }
//
//        DB::commit();
//    }
}