<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmployeeExpense;
use App\Models\User;
use App\Models\EmployeeProfile;

class EmployeeExpenseController extends Controller
{
    public function index(Request $request)
    {
        $expenses = EmployeeExpense::with(['user', 'employeeProfile'])->latest()->get();

        $totalAmount = $expenses->sum('amount');
        $paidAmount = $expenses->where('is_paid', true)->sum('amount');
        $unpaidAmount = $totalAmount - $paidAmount;

        $employees = EmployeeProfile::with('user')->get();

        return view('admin.pages.expenses.index', compact('expenses', 'totalAmount', 'paidAmount', 'unpaidAmount', 'employees'));
    }

    public function store(Request $request)
{
    $request->validate([
        'employee_id' => 'required|exists:employee_profiles,id',
        'type' => 'required|string|in:advance,purchase,item,other',
         'expense_date' => 'nullable|date',
        'amount' => 'required|numeric|min:0.01',
    ]);

    $employee = EmployeeProfile::findOrFail($request->employee_id);

    EmployeeExpense::create([
        'user_id' => $employee->user_id,
        'employee_id' => $employee->id,
        'type' => $request->type,
        'amount' => $request->amount,
        'expense_date' => $request->expense_date ? $request->expense_date : now(),
        'description' => $request->description,
        'is_paid' => $request->is_paid ? true : false,
        'paid_at' => $request->is_paid ? now() : null,
        'payment_method' => $request->payment_method,
        'notes' => $request->notes,
    ]);

    return redirect()->back()->with('success', 'Expense added successfully.');
}

public function update(Request $request, EmployeeExpense $expense)
{
    $request->validate([
        'employee_id' => 'required|exists:employee_profiles,id',
        'type' => 'required|string|in:advance,purchase,item,other',
        'amount' => 'required|numeric|min:0.01',
        'is_paid' => 'nullable|boolean',
        'payment_method' => 'nullable|string|max:255',
        'expense_date' => 'nullable|date',
        'description' => 'nullable|string|max:1000',
        'notes' => 'nullable|string|max:1000',
    ]);

    $expense->update([
        'employee_id' => $request->employee_id,
        'user_id' => EmployeeProfile::find($request->employee_id)->user_id,
        'type' => $request->type,
        'amount' => $request->amount,
        'description' => $request->description,
        'notes' => $request->notes,
        'is_paid' => $request->is_paid ? true : false,
        'expense_date' => $request->expense_date ? $request->expense_date : now(),
        'paid_at' => $request->is_paid ? now() : null,
        'payment_method' => $request->payment_method,
    ]);

    return redirect()->back()->with('success', 'Expense updated successfully.');
}


public function markAsPaid($id)
{
    $expense = EmployeeExpense::findOrFail($id);
    $expense->update(['is_paid' => true, 'paid_at' => now()]);
    return back()->with('success', 'Expense marked as paid.');
}


public function destroy($id)
{
    $expense = EmployeeExpense::findOrFail($id);
    $expense->delete();
    return redirect()->back()->with('success', 'Expense deleted successfully.');
}


}