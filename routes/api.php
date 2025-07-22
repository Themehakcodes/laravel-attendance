<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Api\AdminLoginController;
use App\Http\Controllers\Api\ApiDashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/save-fingerprint', [EmployeeController::class, 'saveFingerprint']);
Route::post('/get-fingerprint', [EmployeeController::class, 'getFingerprintHashes']);
Route::post('/punch-in', [EmployeeController::class, 'punchIn'])->name('employee.attendance.mark');
Route::post('/punch-out', [EmployeeController::class, 'punchOut']);




Route::post('/admin/login', [AdminLoginController::class, 'login']);
Route::post('/admin/dashboard/today-attendance', [ApiDashboardController::class, 'todayAttendance']);
Route::post('/admin/dashboard/today-summary', [ApiDashboardController::class, 'todayAttendanceSummary']);
Route::post('/admin/dashboard/employees', [ApiDashboardController::class, 'getAllEmployees']);
Route::get('/admin/dashboard/employee-expenses', [ApiDashboardController::class, 'getEmployeeExpenses']);
