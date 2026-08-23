<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
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
    protected $table = 'roles';

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
        'name',
        'description',
    ];

    /**
     * The modules that belong to the Role
     */
    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_module', 'role_id', 'module_id', 'id', 'id')
            ->using(RoleModule::class)
            ->withPivot('create', 'read', 'update', 'delete');
    }

    /**
     * Get all of the users for the Role
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role_id', 'id');
    }
}
