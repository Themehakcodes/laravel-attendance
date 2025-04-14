<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SidebarItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'item_name',
        'item_route',
        'item_icon',
        'item_order',
        'item_parent_id',
        'item_permission',
    ];

    // Define relationship with Permission model
    public function permission()
    {
        return $this->belongsTo(Permission::class, 'item_permission');
    }
}
