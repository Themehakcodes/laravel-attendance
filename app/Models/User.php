<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the role of the user (one-to-one).
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get all permissions from the user's role.
     */
    public function permissions()
    {
        return $this->role
            ? $this->role->permissions()->withoutTrashed()
            : collect();
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission($permissionTitle)
    {
        return $this->permissions()->contains('permission_title', $permissionTitle);
    }

    /**
     * Get the employee profile associated with the user.
     */
    public function employeeProfile()
    {
        return $this->hasOne(EmployeeProfile::class, 'user_id', 'id');
    }
}
