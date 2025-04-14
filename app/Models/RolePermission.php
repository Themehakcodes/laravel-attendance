<?php
// app/Models/RolePermission.php
// app/Models/RolePermission.php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\Pivot;

class RolePermission extends Pivot
{
    use SoftDeletes;

    protected $table = 'role_permissions';
}
