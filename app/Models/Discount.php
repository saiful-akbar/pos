<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discount extends Model
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
    protected $table = 'discounts';

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
        'discount',
        'name',
        'effective_date',
        'expiration_date',
    ];

    /**
     * Get all of the saleDetails for the Discount
     */
    public function saleDetails(): HasMany
    {
        return $this->hasMany(SaleDetail::class, 'discount_id', 'id');
    }

    /**
     * Get all of the saleCartDetails for the Discount
     */
    public function saleCartDetails(): HasMany
    {
        return $this->hasMany(SaleCartDetail::class, 'discount_id', 'id');
    }
}
