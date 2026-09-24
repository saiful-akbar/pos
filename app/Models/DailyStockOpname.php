<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyStockOpname extends Model
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
    protected $table = 'daily_stock_opnames';

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
        'user_id',
        'date',
        'total_product',
        'total_system_stock',
        'total_physical_stock',
        'total_difference',
        'notes',
    ];

    /**
     * Get all of the comments for the Customer
     */
    public function details(): HasMany
    {
        return $this->hasMany(DailyStockOpnameDetail::class, 'daily_stock_opname_id', 'id');
    }

    /**
     * Get the user that owns the DailyStockOpname
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the monthlyStock that owns the DailyStockOpname
     */
    public function monthlyStock(): BelongsTo
    {
        return $this->belongsTo(MonthlyStockOpname::class, 'daily_stock_opname_id', 'id');
    }
}
