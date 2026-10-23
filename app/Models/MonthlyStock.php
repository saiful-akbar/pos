<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MonthlyStock extends Model
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
    protected $table = 'monthly_stocks';

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
        'daily_stock_id',
        'year',
        'month',
        'total_product',
        'total_quantity',
        'notes',
    ];

    /**
     * Get all of the details for the MonthlyStock
     */
    public function details(): HasMany
    {
        return $this->hasMany(MonthlyStockDetail::class, 'monthly_stock_id', 'id');
    }

    /**
     * Get the user that owns the MonthlyStock
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the dailyStock that owns the MonthlyStock
     */
    public function daily(): BelongsTo
    {
        return $this->belongsTo(DailyStock::class, 'daily_stock_id', 'id');
    }
}
