<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    public function showLoginForm()
{
    return view('admin.auth.login');
}

public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    if (Auth::guard('admin')->attempt($credentials, $request->remember)) {
        $user = Auth::guard('admin')->user();

        // Check the user's status
        if ($user->user_status === 'deactivated') {
            Auth::guard('admin')->logout();
            return back()->with('error', 'Your account has been deactivated. Please contact support.');
        }

        if ($user->user_status === 'hold') {
            Auth::guard('admin')->logout();
            return back()->with('error', 'Your account is on hold. Please contact support.');
        }

        if ($user->user_status !== 'active') {
            Auth::guard('admin')->logout();
            return back()->with('error', 'Your account is not active. Please contact support.');
        }

        // If the user is active, proceed to the dashboard
        return redirect()->intended(route('dashboard'));

    }

    return back()->with('error', 'Invalid credentials.');
}


}
