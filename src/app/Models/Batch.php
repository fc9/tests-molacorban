<?php

namespace App\Models;

use App\Enums\BatchStatusEnum;
use App\Events\NewBatchFile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{HasMany};
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

class Batch extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Set configs to use uuid.
     */
    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status',
        'path',
        #'filename',
        'errors',
        'ready_at'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'ready_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'ready_at' => 'datetime:d/m/Y H:i:s',
        'created_at' => 'datetime:d/m/Y H:i:s',
        'updated_at' => 'datetime:d/m/Y H:i:s',
        'deleted_at' => 'datetime:d/m/Y H:i:s',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            $model->setAttribute($model->getKeyName(), Uuid::uuid4());
        });
    }

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => NewBatchFile::class
    ];

    /**
     * Get all of the purchases for the batch.
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * Get BatchStatus object enum
     */
    public function getStatusAttribute($value): BatchStatusEnum
    {
        return BatchStatusEnum::fromValue(intval($value));
    }

    /**
     * Get errors json object
     */
    public function getErrorsAttribute($value)
    {
        return json_decode($value);
    }

    /**
     * Set errors
     */
    public function setErrorsAttribute($value)
    {
        $this->attributes['errors'] = json_encode($value);
    }
}
