@extends('admin.layouts.app')
@section('title', 'Expense Overview')
@section('admincontent')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white shadow rounded-4">
                <div class="card-body">
                    <h5>Total Expenses</h5>
                    <h3>₹{{ number_format($totalAmount, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white shadow rounded-4">
                <div class="card-body">
                    <h5>Paid</h5>
                    <h3>₹{{ number_format($paidAmount, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-danger text-white shadow rounded-4">
                <div class="card-body">
                    <h5>Unpaid</h5>
                    <h3>₹{{ number_format($unpaidAmount, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between mb-3">
        <h4>Expense Records</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#expenseModal">Add Expense</button>
    </div>

    <div class="card shadow rounded-4">
        <div class="card-body table-responsive">
            <table class="table table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Employee</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Paid</th>
                        <th>Method</th>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $index => $expense)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $expense->employeeProfile->employee_name ?? $expense->user->name }}</td>
                            <td>{{ ucfirst($expense->type) }}</td>
                            <td>₹{{ number_format($expense->amount, 2) }}</td>
                            <td>
                                @if ($expense->is_paid)
                                    <span class="badge bg-success">Yes</span><br>
                                    <small>{{ $expense->paid_at->format('d M, Y') }}</small>
                                @else
                                    <span class="badge bg-warning text-dark">No</span>
                                @endif
                            </td>
                            <td>{{ $expense->payment_method ?? '—' }}</td>
                            <td>{{ $expense->created_at->format('d M, Y') }}</td>
                            <td>{{ $expense->description ?? '—' }}</td>
                            <td>
                                @if (!$expense->is_paid)
                                    <form action="{{ route('expenses.markAsPaid', $expense->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-success">Mark as Paid</button>
                                    </form>
                                @endif
                                <button class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#editExpenseModal{{ $expense->id }}">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center">No expenses found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Expense Modal -->
<div class="modal fade" id="expenseModal" tabindex="-1" aria-labelledby="expenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('expenses.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Add Expense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-md-6">
                    <label>Employee</label>
                    <select name="employee_id" class="form-select" required>
                        <option value="">Select Employee</option>
                        @foreach ($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->employee_name }} ({{ $emp->user->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label>Type</label>
                    <select name="type" class="form-select" required>
                        <option value="advance">Advance</option>
                        <option value="purchase">Purchase</option>
                        <option value="item">Item</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label>Amount</label>
                    <input type="number" name="amount" class="form-control" step="0.01" required>
                </div>
                <div class="col-md-6">
                    <label>Payment Method</label>
                    <input type="text" name="payment_method" class="form-control" placeholder="e.g. Cash, UPI">
                </div>
                <div class="col-md-6">
                    <label>Is Paid?</label>
                    <select name="is_paid" class="form-select">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>
                <div class="col-12">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary">Save Expense</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- All Edit Modals -->
@foreach ($expenses as $expense)
    <div class="modal fade" id="editExpenseModal{{ $expense->id }}" tabindex="-1" aria-labelledby="editExpenseLabel{{ $expense->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('expenses.update', $expense->id) }}" method="POST" class="modal-content">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editExpenseLabel{{ $expense->id }}">Edit Expense</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label>Employee</label>
                        <select name="employee_id" class="form-select" required>
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->id }}" {{ $emp->id == $expense->employee_id ? 'selected' : '' }}>
                                    {{ $emp->employee_name }} ({{ $emp->user->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Type</label>
                        <select name="type" class="form-select" required>
                            @foreach (['advance', 'purchase', 'item', 'other'] as $type)
                                <option value="{{ $type }}" {{ $expense->type == $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Amount</label>
                        <input type="number" name="amount" class="form-control" value="{{ $expense->amount }}" step="0.01" required>
                    </div>
                    <div class="col-md-6">
                        <label>Payment Method</label>
                        <input type="text" name="payment_method" class="form-control" value="{{ $expense->payment_method }}">
                    </div>
                    <div class="col-md-6">
                        <label>Is Paid?</label>
                        <select name="is_paid" class="form-select">
                            <option value="0" {{ !$expense->is_paid ? 'selected' : '' }}>No</option>
                            <option value="1" {{ $expense->is_paid ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="2">{{ $expense->description }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
@endforeach

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if (session('success'))
        Swal.fire({ icon: 'success', title: 'Success', text: '{{ session('success') }}' });
    @endif
    @if (session('error'))
        Swal.fire({ icon: 'error', title: 'Error', text: '{{ session('error') }}' });
    @endif
</script>
@endsection
