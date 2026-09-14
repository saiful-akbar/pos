<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleReturnDetail extends Model
{
    use HasUlids, SoftDeletes;

    /**
     * Disable auto increment
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Enable created_at and updated_at
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Connection name
     *
     * @var string
     */
    protected $connection = 'pos';

    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'sale_return_details';

    /**
     * Primary key
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Tipe primary
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sale_return_id',
        'product_id',
        'quantity',
        'purchase_price',
        'selling_price',
        'discount',
        'subtotal',
    ];

    /**
     * Get the sale that owns the SaleDetail
     */
    public function saleReturn(): BelongsTo
    {
        return $this->belongsTo(SaleReturn::class, 'sale_return_id', 'id');
    }

    /**
     * Get the product that owns the SaleDetail
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
