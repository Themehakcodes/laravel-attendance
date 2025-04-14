<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\Models\RolePermission;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;

class RolesController extends Controller
{
   public function index()
   {
      $roles = Role::all();
      $permissions = Permission::all();
      return view('admin.pages.roles.index', compact('roles','permissions'));
   }

   public function store(Request $request)
   {
       // Validate the incoming request
       $request->validate([
           'role_name' => 'required|string|max:255',
           'description' => 'nullable|string|max:1000',
           'permissions' => 'required|array',
           'permissions.*' => 'exists:permissions,id', // Ensure the permission IDs are valid
       ]);

       // Create a new role
       $role = Role::create([
           'role_name' => $request->role_name,
           'description' => $request->description,
       ]);

       // Attach the selected permissions to the role
       $role->Permission()->sync($request->permissions); // This will attach the permissions via a pivot table

       // Return a success response or redirect
       return redirect()->route('admin.roles.index')->with('success', 'Role created and permissions assigned successfully!');
   }

   public function update(Request $request, $id)
   {
       $request->validate([
           'role_name' => 'required|string|max:255',
           'description' => 'nullable|string',
           'permissions' => 'nullable|array',
           'permissions.*' => 'exists:permissions,id',
       ]);

       $role = Role::findOrFail($id);

       $role->update([
           'role_name' => $request->role_name,
           'description' => $request->description,
       ]);

       $newPermissionIds = $request->permissions ?? [];

       // Fetch all current permission relationships (including soft deleted)
       $existingRelations = RolePermission::withTrashed()
           ->where('role_id', $role->id)
           ->get();

       // Get currently attached permission IDs
       $existingPermissionIds = $existingRelations->pluck('permission_id')->toArray();

       // Identify to soft delete
       $toDelete = array_diff($existingPermissionIds, $newPermissionIds);
       foreach ($toDelete as $permissionId) {
           RolePermission::where('role_id', $role->id)
               ->where('permission_id', $permissionId)
               ->delete(); // soft delete
       }

       // Restore soft-deleted ones if they're being re-added
       foreach ($newPermissionIds as $permissionId) {
           RolePermission::withTrashed()
               ->updateOrCreate(
                   ['role_id' => $role->id, 'permission_id' => $permissionId],
                   ['deleted_at' => null, 'updated_at' => Carbon::now()]
               );
       }

       return redirect()->back()->with('success', 'Role updated and permissions synced with soft delete logic.');
   }

}
