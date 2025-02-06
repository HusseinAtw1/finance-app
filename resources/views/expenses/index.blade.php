@extends('welcome')

@section('content')
<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Your Expenses</h2>
        <a href="{{ route('expenses.create') }}" class="btn btn-primary">Add an Expense</a>
    </div>

    <!-- Filter and Search Form -->
    <form method="GET" action="{{ route('expenses.index') }}">
        <div class="row mb-3">
            <!-- Account Filter -->
            <div class="col-md-3">
                <label for="account_id" class="form-label">Account:</label>
                <select name="account_id" id="account_id" class="form-control" onchange="this.form.submit()">
                    <option value="">All Accounts</option>
                    @foreach($accounts as $account)
                        <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>
                            {{ $account->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div class="col-md-3">
                <label for="status" class="form-label">Status:</label>
                <select name="status" id="status" class="form-control" onchange="this.form.submit()">
                    <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>All</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                </select>
            </div>

            <!-- Search Field -->
            <div class="col-md-4">
                <label for="search" class="form-label">Search Expenses:</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Search by name or description" value="{{ request('search') }}">
            </div>

            <!-- Submit Button (if needed) -->
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-secondary w-100">Search</button>
            </div>
        </div>
    </form>

    <div class="row">
        @forelse ($expenses as $expense)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Expense ID: {{ $expense->id }}</span>
                        <a href="{{ route('expenses.show', $expense->id) }}" class="btn btn-primary btn-sm">View</a>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><strong>Name:</strong> {{ $expense->name }}</p>
                        <p class="card-text">
                            <strong>Amount:</strong> {{ $expense->currency }} {{ number_format($expense->amount, 2) }}
                        </p>
                        <p class="card-text"><strong>Description:</strong> {{ $expense->description }}</p>
                        <p class="card-text"><strong>Date:</strong> {{ $expense->created_at->format('M d, Y') }}</p>
                        <p class="card-text">
                            <strong>Status:</strong>
                            <span class="badge
                                {{ $expense->status == 'paid' ? 'bg-success' : ($expense->status == 'overdue' ? 'bg-danger' : 'bg-warning') }}">
                                {{ ucfirst($expense->status) }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p>No expenses found with the selected filters.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $expenses->appends(request()->query())->links() }}
    </div>
</div>
@endsection
