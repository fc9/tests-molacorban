<?php

namespace App\Models;

use App\Enums\PurchaseStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasOne};
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Disabling Auto Timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'batch_uuid',
        'supplier_id',
        'product_id',
        'customer_id',
        'value', #original
        'rate', #original
        'status', #situation
        'date',
        'returned_at',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'date',
        'returned_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'datetime:d/m/Y H:i:s',
        'returned_at' => 'datetime:d/m/Y H:i:s',
        'deleted_at' => 'datetime:d/m/Y H:i:s',
    ];

    /**
     * Get the purchase's batch.
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class, 'batch_uuid', 'uuid');
    }

    /**
     * Get the purchase's supplier.
     */
    public function supplier(): HasOne
    {
        return $this->hasOne(Supplier::class);
    }

    /**
     * Get the purchase's product.
     */
    public function product(): HasOne
    {
        return $this->hasOne(Product::class);
    }

    /**
     * Get the purchase's customer.
     */
    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class);
    }

    /**
     * Get the purchase's payment.
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Get PurchaseStatus object enum
     */
    public function getStatusAttribute($value): PurchaseStatusEnum
    {
        return PurchaseStatusEnum::fromValue(intval($value));
    }
}
