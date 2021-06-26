<?php

namespace App\Listeners;

use App\Events\NewBatchFile;
use App\Jobs\LoadBatchFilesJob;
use App\Models\Batch;

class ProcessBatchFiles
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NewBatchFile  $event
     * @return false
     */
    public function handle(NewBatchFile $event): bool
    {
        if ($event->batch instanceof Batch) {
            dispatch((new LoadBatchFilesJob()));
        }

        return false;
    }
}
