<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmployeeProfile;

class EmployeeController extends Controller
{
  public function index()
  {
    // Fetch all employees from the database
    $employees = EmployeeProfile::all();

    // Return the view with the employees data
    return view('admin.employees.index', compact('employees'));
  }
}
