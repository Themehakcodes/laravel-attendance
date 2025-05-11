<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\User;

class Usercontroller extends Controller
{
   public function index()
   {
    $users = User::with('role')->paginate(10);
       $roles = Role::all();
       return view('admin.pages.users.index', compact('users', 'roles'));
   }
   public function store(Request $request)
   {
       $request->validate([
           'name' => 'required|string|max:255',
           'email' => 'required|email|unique:users,email',
           'password' => 'required|string|min:6',
           'role_id' => 'required|exists:roles,id', // Ensure role_id exists in the roles table
           'user_status' => 'required|in:active,deactivated,hold',
       ]);

       // Check if the role exists
       $role = Role::find($request->role_id);
       if (!$role) {
           return redirect()->back()->with('error', 'Role not found.');
       }

       // Clean up the name and get the first 4 characters in uppercase
       $cleanName = preg_replace('/\s+/', '', $request->name);
       $prefix = strtoupper(substr($cleanName, 0, 4));

       // Generate a random 4-digit number for user_id
       $randomDigits = rand(1000, 9999);
       $user_id = $prefix . $randomDigits;

       // Create the user
       $user = User::create([
           'user_id' => $user_id,
           'name' => $cleanName,
           'email' => $request->email,
           'password' => bcrypt($request->password),
           'role_id' => $request->role_id, // Directly assign the role_id
           'user_status' => $request->user_status,
       ]);

       return redirect()->back()->with('success', 'User created successfully.');
   }


    public function update(Request $request, $id)
    {
         $request->validate([
              'name' => 'required|string|max:255',
              'email' => 'required|email|unique:users,email,' . $id,
              'role_id' => 'required|exists:roles,id',
              'user_status' => 'required|in:active,deactivated,hold',
         ]);

         // Check if the role exists
         $role = Role::find($request->role_id);
         if (!$role) {
              return redirect()->back()->with('error', 'Role not found.');
         }

         // Update the user
         $user = User::findOrFail($id);
         $user->update([
              'name' => $request->name,
              'email' => $request->email,
              'role_id' => $request->role_id,
              'user_status' => $request->user_status,
         ]);

         return redirect()->back()->with('success', 'User updated successfully.');
    }





}
