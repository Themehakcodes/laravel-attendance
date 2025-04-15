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
        'role_id', // Include this if user has a role_id column
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
     * Get the role of the user.
     */
    // In User model
public function roles()
{
    return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
}
   // In the User model
public function role()
{
    return $this->belongsTo(Role::class); // One role for the user
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
     * Check if user has a specific permission (optional utility).
     */
    public function hasPermission($permissionTitle)
    {
        return $this->permissions()->contains('permission_title', $permissionTitle);
    }
}
