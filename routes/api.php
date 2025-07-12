<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\EmployeeController;

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
