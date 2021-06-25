<?php

namespace App\Console\Commands;

use App\Enums\BatchStatusEnum;
use App\Http\Requests\PurchaseRequest;
use App\Repositories\BatchRepository;
use App\Repositories\PurchaseRepository;
use App\Services\BatchCSVService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LoadFilesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'load:files {--batch=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Loading batches that have not yet been saved to the bank.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $batch = $this->option('batch');

        if ($batch !== null) {
            $batchRepository =  new BatchRepository;
            $status = BatchStatusEnum::keys2Values(array_values(explode(',', $batch)));
            $batches = $batchRepository->find(['status' => $status])->get();

            foreach ($batches as $batch) {
                $batchRepository->store(['status' => BatchStatusEnum::LOADING], $batch);
                $records = BatchCSVService::readerByPath($batch->path, ',')->getRecords();
                $errors = [];

                DB::beginTransaction();
                foreach ($records as $index => $record) {
                    $record['batch_uuid'] = $batch->uuid;
                    $validator = Validator::make($record, (new PurchaseRequest)->rules());

                    if ($validator->fails()) {
                        $errors = $errors + $validator->errors()->messages();
                    } elseif (count($errors) === 0) {
                        (new PurchaseRepository)->store($record);
                    }
                }

                if (count($errors) === 0) {
                    DB::commit();
                    $batchRepository->store(['status' => BatchStatusEnum::DONE, 'ready_at' => Carbon::now()], $batch);
                } else {
                    DB::rollBack();
                    $errors = array_map(function ($item) {
                        return implode(',', $item);
                    }, $errors);
                    $batchRepository->store(['status' => BatchStatusEnum::ERROR, 'errors' => $errors], $batch);
                }
            }
        }

        return 0;
    }
}
