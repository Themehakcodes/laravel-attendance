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
            'employee_management',
            'user_management',
            'management',
            'role_management',
            'permission_management',
            'attendance_management',
            'print_management'
            
        ];

        $actions = ['create', 'read', 'update'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'permission_title' => "{$module}_{$action}"
                ]);
            }
        }
    }
}
