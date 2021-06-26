<?php

namespace Tests\Unit\App\Events;

use App\Enums\BatchStatusEnum;
use App\Events\NewBatchFile;
use App\Models\Batch;
use Illuminate\Support\Facades\Event;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class NewBatchFileTest extends TestCase
{
    /** @var Batch */
    protected $batch;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->batch = Batch::factory()->make();
    }

    /** @test */
    public function check_if_NewBatchFile_event_dispatched()
    {
        Event::fake();

        $batch = (new Batch)->fill($this->batch->toArray());
        $batch->uuid = Uuid::uuid4();
        $batch->save();

        Event::assertDispatched(function (NewBatchFile $event) use ($batch) {
            return $event->batch->id === $batch->id;
        });

        Event::assertDispatched(NewBatchFile::class, 1);

        $batch->delete();
    }
}