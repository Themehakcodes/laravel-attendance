<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\AttendanceMarker;
use App\Http\Controllers\Admin\Usercontroller;
use App\Http\Controllers\Admin\EmployeeExpenseController;

use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\PrintController;
use App\Http\Controllers\Admin\EmployeeProfileController;
use App\Http\Controllers\Admin\PermissionsController;


use App\Http\Controllers\Employee\AttendanceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('admin.pages.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/employee/dashboard', function () {
    return view('employee.pages.dashboard');
})->middleware(['auth', 'verified'])->name('employee.dashboard');

Route::get('admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('admin/login', [AdminAuthController::class, 'login']);

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/permissions', [PermissionsController::class, 'index'])->name('permissions.index');
    Route::post('/permissions/store', [PermissionsController::class, 'store'])->name('permissions.store');
    Route::put('/permissions/{id}', [PermissionsController::class, 'update'])->name('permissions.update');
    Route::get('/roles', [RolesController::class, 'index'])->name('roles.index');
    Route::post('/roles', [RolesController::class, 'store'])->name('roles.store');
    Route::put('/roles/{id}', [RolesController::class, 'update'])->name('roles.update');
    Route::get('/users', [Usercontroller::class, 'index'])->name('users.index');
    Route::post('/users', [Usercontroller::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [Usercontroller::class, 'update'])->name('users.update');
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/employees/store', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/employees/{id}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('/employees/{id}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::patch('/employees/{id}/status', [EmployeeController::class, 'updatestatus'])->name('employees.updatestatus');
    Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    Route::get('/attendance/calandar', [AttendanceMarker::class, 'calendar'])->name('attendance.calendar');
    Route::post('/attendance/punchin', [AttendanceMarker::class, 'punchIn'])->name('attendance.punchin');
Route::post('/attendance/punchout', [AttendanceMarker::class, 'punchOut'])->name('attendance.punchout');

Route::get('/attendance', [AttendanceMarker::class, 'index'])->name('attendance.index');
    Route::post('/attendance/mark', [AttendanceMarker::class, 'mark'])->name('attendance.mark');

        Route::get('/expenses', [EmployeeExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/expenses', [EmployeeExpenseController::class, 'store'])->name('expenses.store');
    Route::put('/expenses/{expense}', [EmployeeExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('/expenses/{id}', [EmployeeExpenseController::class, 'destroy'])->name('expenses.destroy');
    Route::patch('/expenses/{id}/mark-paid', [EmployeeExpenseController::class, 'markAsPaid'])->name('expenses.markAsPaid');
    Route::get('/employee/profile/{employee_id}', [EmployeeProfileController::class, 'show'])->name('employee.profile.show');

    Route::get('/employee/attendancecards', [PrintController::class, 'attendancecards'])->name('employee.attendancecards');
    Route::get('/attendance-card/{employee_id}', [PrintController::class, 'printSinglepreview'])->name('attendancecard.single.preview');
    Route::get('/attendance-card/print/{employee_id}', [PrintController::class, 'printSingle'])->name('attendancecard.single');

    


Route::get('/employee/cardattendance', [AttendanceMarker::class, 'attendanceCards'])->name('employee.attendance.attendanceCards');
Route::post('/attendance/mark/cards', [AttendanceMarker::class, 'markcardattendance'])->name('attendance.mark.cards');







Route::get('/employee/attendance', [AttendanceController::class, 'index'])->name('employee.attendance.index');
Route::post('/employee/attendance/punch-in', [AttendanceController::class, 'punchIn'])->name('employee.attendance.punchIn');
});





require __DIR__.'/auth.php';
