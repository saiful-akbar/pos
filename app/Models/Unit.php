<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
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
    protected $table = 'units';

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
        'code',
        'name',
        'is_active',
    ];

    /**
     * Get all of the prices for the Unit
     */
    public function prices(): HasMany
    {
        return $this->hasMany(Price::class, 'unit_id', 'id');
    }

    /**
     * Get all of the purchaseCartDetails for the Unit
     */
    public function purchaseCartDetails(): HasMany
    {
        return $this->hasMany(PurchaseCartDetail::class, 'unit_id', 'id');
    }

    /**
     * Get all of the purchaseDetails for the Unit
     */
    public function purchaseDetails(): HasMany
    {
        return $this->hasMany(PurchaseDetail::class, 'unit_id', 'id');
    }

    /**
     * Get all of the saleCartDetails for the Unit
     */
    public function saleCartDetails(): HasMany
    {
        return $this->hasMany(SaleCartDetail::class, 'unit_id', 'id');
    }

    /**
     * Get all of the saleDetails for the Unit
     */
    public function saleDetails(): HasMany
    {
        return $this->hasMany(SaleDetail::class, 'unit_id', 'id');
    }

    /**
     * Get all of the saleReturnCartDetails for the Unit
     */
    public function saleReturnCartDetails(): HasMany
    {
        return $this->hasMany(SaleReturnCartDetail::class, 'unit_id', 'id');
    }

    /**
     * Get all of the saleReturnDetails for the Unit
     */
    public function saleReturnDetails(): HasMany
    {
        return $this->hasMany(SaleReturnDetail::class, 'unit_id', 'id');
    }
}
