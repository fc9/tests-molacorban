<?php

namespace App\Console\Commands;

use App\Enums\BatchStatusEnum;
use App\Repositories\BatchRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DeleteFilesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:files {--batch=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete files.';

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
     * Delete disposable batch's files.
     *
     * @return int
     */
    public function handle()
    {
        $batch = $this->option('batch');

        if ($batch !== null) {
            $status = BatchStatusEnum::keys2Values(array_values(explode(',', $batch)));
            $batches = (new BatchRepository)->find(['status' => $status])->get();

            foreach ($batches as $batch) {
                Storage::delete($batch->path);
                Storage::delete('app/' . $batch->path);
            }
        }

        return 0;
    }
}
