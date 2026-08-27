<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
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
    protected $table = 'products';

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
        'category_id',
        'unit_id',
        'code',
        'barcode',
        'name',
        'purchase_price',
        'selling_price',
        'discount',
        'minimum_stock',
        'current_stock',
        'is_active',
    ];

    /**
     * Get the category that owns the Product
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    /**
     * Get the unit that owns the Product
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    /**
     * Get all of the saleDetails for the Product
     */
    public function saleDetails(): HasMany
    {
        return $this->hasMany(SaleDetail::class, 'product_id', 'id');
    }

    /**
     * Get all of the purchaseDetails for the Product
     */
    public function purchaseDetails(): HasMany
    {
        return $this->hasMany(PurchaseDetail::class, 'product_id', 'id');
    }

    /**
     * Get all of the stockMovements for the Product
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'product_id', 'id');
    }

    /**
     * Get all of the stockOpnameDetails for the Product
     */
    public function stockOpnameDetails(): HasMany
    {
        return $this->hasMany(StockOpnameDetail::class, 'product_id', 'id');
    }
}
