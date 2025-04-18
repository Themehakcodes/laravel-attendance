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
                'id' => 1,
                'item_name' => 'Dashboard',
                'item_route' => '/dashboard',
                'item_icon' => 'fas fa-tachometer-alt', // Font Awesome icon
                'permission_title' => 'dashboard_management_read',
                'item_order' => 1,
                'item_parent_id' => 0,
                'created_at' => '2025-04-14 09:16:51',
                'updated_at' => '2025-04-14 09:28:41',
                'deleted_at' => NULL,
            ],
            [
                'id' => 2,
                'item_name' => 'User',
                'item_route' => '/users',
                'item_icon' => 'fas fa-users', // Font Awesome icon
                'permission_title' => 'user_management_read',
                'item_order' => 0,
                'item_parent_id' => 5,
                'created_at' => '2025-04-14 09:16:51',
                'updated_at' => '2025-04-14 09:28:06',
                'deleted_at' => NULL,
            ],
            [
                'id' => 3,
                'item_name' => 'Role',
                'item_route' => '/roles',
                'item_icon' => 'fas fa-shield-alt', // Font Awesome icon
                'permission_title' => 'role_management_read',
                'item_order' => 0,
                'item_parent_id' => 5,
                'created_at' => '2025-04-14 09:16:51',
                'updated_at' => '2025-04-14 09:28:06',
                'deleted_at' => NULL,
            ],
            [
                'id' => 4,
                'item_name' => 'Permission',
                'item_route' => '/permissions',
                'item_icon' => 'fas fa-lock', // Font Awesome icon
                'permission_title' => 'permission_management_read',
                'item_order' => 0,
                'item_parent_id' => 5,
                'created_at' => '2025-04-14 09:16:51',
                'updated_at' => '2025-04-14 09:28:07',
                'deleted_at' => NULL,
            ],
            [
                'id' => 5,
                'item_name' => 'Management',
                'item_route' => '/Management',
                'item_icon' => 'fas fa-cogs', // Font Awesome icon
                'permission_title' => 'management_read',
                'item_order' => 2,
                'item_parent_id' => 0,
                'created_at' => '2025-04-14 09:28:41',
                'updated_at' => '2025-04-14 09:28:41',
                'deleted_at' => NULL,
            ],
            [
                'id' => 6,
                'item_name' => 'Employees',
                'item_route' => '/employees',
                'item_icon' => 'fas fa-users',
                'permission_title' => 'employee_management_read',
                'item_order' => 1,
                'item_parent_id' => 0,
                'created_at' => '2025-04-14 09:28:41',
                'updated_at' => '2025-04-14 09:28:41',
                'deleted_at' => NULL,
            ]
        ];

        foreach ($items as $item) {
            // Find the permission based on the permission title
            $permission = Permission::where('permission_title', $item['permission_title'])->first();

            // If the permission exists, create or update the sidebar item
            if ($permission) {
                SidebarItem::updateOrCreate(
                    ['id' => $item['id']],  // Ensuring we update the correct entry based on `id`
                    [
                        'item_name' => $item['item_name'],
                        'item_route' => $item['item_route'],
                        'item_icon' => $item['item_icon'],
                        'item_order' => $item['item_order'],
                        'item_parent_id' => $item['item_parent_id'],
                        'item_permission' => $permission->id, // Link with the actual permission ID
                        'created_at' => $item['created_at'],
                        'updated_at' => $item['updated_at'],
                        'deleted_at' => $item['deleted_at'],
                    ]
                );
            }
        }
    }
}
