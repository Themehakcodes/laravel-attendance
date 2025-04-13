<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use HasFactory, SoftDeletes;

    // Define the table name (if it's not the plural form of the model name)
    protected $table = 'permissions';

    // The attributes that are mass assignable
    protected $fillable = [
        'permission_title',
    ];

    // The attributes that should be cast to native types
    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    // Optionally, you can add any custom methods here if needed
}
