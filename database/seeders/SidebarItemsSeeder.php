<?php

namespace Database\Seeders;

use App\Models\SidebarItem;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class SidebarItemsSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'item_name' => 'Dashboard',
                'item_route' => '/dashboard',
                'item_icon' => 'fas fa-tachometer-alt', // Font Awesome icon
                'permission_title' => 'dashboard_management_read',
                'item_order' => 0,
                'item_parent_id' => 0,
            ],
            [
                'item_name' => 'User Management',
                'item_route' => '/users',
                'item_icon' => 'fas fa-users', // Font Awesome icon
                'permission_title' => 'user_management_read',
                'item_order' => 1,
                'item_parent_id' => 0,
            ],
            [
                'item_name' => 'Role Management',
                'item_route' => '/roles',
                'item_icon' => 'fas fa-shield-alt', // Font Awesome icon
                'permission_title' => 'role_management_read',
                'item_order' => 2,
                'item_parent_id' => 0,
            ],
            [
                'item_name' => 'Permission Management',
                'item_route' => '/permissions',
                'item_icon' => 'fas fa-lock', // Font Awesome icon
                'permission_title' => 'permission_management_read',
                'item_order' => 3,
                'item_parent_id' => 0,
            ],
        ];

        foreach ($items as $item) {
            $permission = Permission::where('permission_title', $item['permission_title'])->first();

            if ($permission) {
                SidebarItem::updateOrCreate(
                    ['item_route' => $item['item_route']],
                    [
                        'item_name' => $item['item_name'],
                        'item_icon' => $item['item_icon'], // Use Font Awesome icon
                        'item_order' => $item['item_order'],
                        'item_parent_id' => $item['item_parent_id'],
                        'item_permission' => $permission->id, // Use actual ID
                    ]
                );
            }
        }
    }
}
