<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoleUser extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'role_user';

    protected $fillable = [
        'user_id',
        'role_id',
    ];

    /**
     * Role relationship
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    /**
     * User relationship
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
