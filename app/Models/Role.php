<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Permission;
use App\Models\RolePermission;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'role_name',
        'description',
    ];

    /**
     * The users that belong to this role.
     */
  // In Role model
public function users()
{
    return $this->belongsToMany(User::class, 'role_user', 'role_id', 'user_id');
}


    // In Role.php model

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions')
                    ->using(RolePermission::class)
                    ->withTimestamps()
                    ->withPivot('deleted_at')
                    ->wherePivotNull('deleted_at'); // This is equivalent to withoutTrashed()
    }



}
