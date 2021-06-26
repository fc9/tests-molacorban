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
        $obj = $model ?? (new $this->model);
        $obj->fill($payload)->save();

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
}