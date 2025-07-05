<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeProfile;
use Illuminate\Http\Request;

class PrintController extends Controller
{
public function attendancecards(Request $request)
{
    $search = $request->input('search');

    $query = EmployeeProfile::where('staff_status', 'active');

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('employee_name', 'like', "%$search%")
              ->orWhere('employee_id', 'like', "%$search%");
        });
    }

    $employees = $query->orderByDesc('created_at')->paginate(10);

    return view('admin.pages.print.attendancecard', compact('employees', 'search'));
}


public function printSinglepreview(Request $request, $employee_id)
{
    $employee = EmployeeProfile::where('employee_id', $employee_id)
        ->where('staff_status', 'active')
        ->firstOrFail();

    return view('admin.pages.print.singlecardpreview', compact('employee'));
}

public function  printSingle(Request $request, $employee_id){
     $employee = EmployeeProfile::where('employee_id', $employee_id)
        ->where('staff_status', 'active')
        ->firstOrFail();

    return view('admin.pages.print.singlecard', compact('employee'));
}

}
