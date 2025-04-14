<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $modules = [
            'dashboard_management',
            'user_management',
            'role_management',
            'permission_management',
        ];

        $actions = ['create', 'read', 'update', 'delete'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'permission_title' => "{$module}_{$action}"
                ]);
            }
        }
    }
}
