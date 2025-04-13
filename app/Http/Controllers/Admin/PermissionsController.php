<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Permission;

class PermissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions = Permission::latest()->paginate(10); // Optional pagination
        return view('admin.pages.permissions.index', compact('permissions'));
    }   

    public function store(Request $request)
    {
        // Validate the input
        $validated = $request->validate([
            'permission_title' => 'required|string|max:255',
        ]);

        // Create a new permission
        Permission::create($validated);

        // Redirect back with a success message
        return redirect()->route('permissions.index')->with('success', 'Permission created successfully!');
    }

    public function update(Request $request, $id)
{
    // Validate the input data
    $validated = $request->validate([
        'permission_title' => 'required|string|max:255',
    ]);

    // Find the permission to update
    $permission = Permission::findOrFail($id);

    // Update the permission with the new data
    $permission->update([
        'permission_title' => $validated['permission_title'],
    ]);

    // Redirect back with success message
    return redirect()->route('permissions.index')->with('success', 'Permission updated successfully!');
}

}
