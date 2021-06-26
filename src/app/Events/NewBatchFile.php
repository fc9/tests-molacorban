<?php

namespace App\Events;

use App\Models\Batch;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewBatchFile
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The contact sent.
     *
     * @var Batch
     */
    public Batch $batch;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Batch $batch)
    {
        $this->batch = $batch;
    }

    /**
     * Batch
     *
     * @return Batch $batch
     */
    public function batch(): Batch
    {
        return $this->batch;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
