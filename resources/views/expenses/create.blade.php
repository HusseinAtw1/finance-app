@extends('welcome')

@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Whoops! Something went wrong.</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('expenses.store') }}" method="POST" class="mt-4">
    <div class="container-md mx-auto" style="max-width: 800px;">
        <h2 class="mb-4 text-center">Add New Expense</h2>
        @csrf

        <!-- Account Selection Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Select the account from which this expense will be deducted
                </div>
                <div class="form-floating">
                    <select name="account_id" id="account_id" class="form-select" required>
                        <option value="">Choose an account</option>
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}">
                                {{ $account->name }}
                            </option>
                        @endforeach
                    </select>
                    <label for="account_id">Select Account</label>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <!-- Expense Name -->
            <div class="col-12">
                <div class="form-floating">
                    <input type="text" class="form-control" id="name" name="name" placeholder="Expense Name" required>
                    <label for="name">Expense Name</label>
                </div>
            </div>

            <!-- Currency (Input Field) -->
            <div class="form-floating mb-3">
                <select name="currency_id" id="currency_id" class="form-control">
                    <option value="">Select Currency</option>
                    @foreach($currencies as $currency)
                        <option value="{{ $currency->id }}">{{ $currency->name }} - {{ $currency->full_name }}</option>
                    @endforeach
                </select>
                <label for="currency_id">Select Currency</label>
            </div>


            <!-- Amount -->
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="number" class="form-control" id="amount" name="amount" step="0.01" placeholder="Amount" required>
                    <label for="amount">Amount</label>
                </div>
            </div>

            <!-- Paid Amount (Optional) -->
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="number" class="form-control" id="paid_amount" name="paid_amount" step="0.01" placeholder="Paid Amount">
                    <label for="paid_amount">Paid Amount (Optional)</label>
                </div>
            </div>

            <!-- Status -->
            <div class="col-md-6">
                <div class="form-floating">
                    <select class="form-select" id="status" name="status" required>
                        <option value="pending" selected>Pending</option>
                        <option value="overdue">Overdue</option>
                        <option value="paid">Paid</option>
                    </select>
                    <label for="status">Status</label>
                </div>
            </div>

            <!-- Due Date -->
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="date" class="form-control" id="due_date" name="due_date" placeholder="Due Date">
                    <label for="due_date">Due Date</label>
                </div>
            </div>

            <!-- Paid At -->
            <div class="col-md-6">
                <div class="form-floating">
                    <!-- datetime-local expects a format like "2025-02-03T14:30" -->
                    <input type="datetime-local" class="form-control" id="paid_at" name="paid_at" placeholder="Paid At">
                    <label for="paid_at">Paid At</label>
                </div>
            </div>

            <!-- Expense Category -->
            <div class="col-md-6">
                <div class="form-floating">
                    <select class="form-select" id="category" name="category" required>
                        <option value="general" selected>General</option>
                        <option value="food">Food</option>
                        <option value="transportation">Transportation</option>
                        <option value="utilities">Utilities</option>
                        <option value="entertainment">Entertainment</option>
                        <!-- Add additional categories as needed -->
                    </select>
                    <label for="category">Expense Category</label>
                </div>
            </div>

            <!-- Description -->
            <div class="col-12">
                <div class="form-floating">
                    <textarea class="form-control" id="description" name="description" style="height: 100px" placeholder="Additional Notes"></textarea>
                    <label for="description">Description</label>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="col-12 mt-4">
                <div class="d-grid gap-2 col-md-6 mx-auto">
                    <button type="submit" class="btn btn-primary btn-lg py-2 fw-bold">
                        <i class="bi bi-plus-circle me-2"></i>Add Expense
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
