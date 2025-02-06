@extends('welcome')

@section('content')
<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Your Liabilities</h2>
        <a href="{{ route('liabilities.create') }}" class="btn btn-primary">Add a Liability</a>
    </div>

    <!-- Account Selection, Status Filter, and Search Form -->
    <form method="GET" action="{{ route('liabilities.index') }}">
        <div class="row mb-3">
            <!-- Account Selection -->
            <div class="col-md-3">
                <label for="account_id" class="form-label">Select Account:</label>
                <select name="account_id" id="account_id" class="form-control" onchange="this.form.submit()">
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}" {{ $selectedAccount == $account->id ? 'selected' : '' }}>
                            {{ $account->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Liability Filter -->
            <div class="col-md-3">
                <label for="filter_status" class="form-label">Liability Filter:</label>
                <select name="filter_status" id="filter_status" class="form-control" onchange="this.form.submit()">
                    <option value="all" {{ request('filter_status', 'all') == 'all' ? 'selected' : '' }}>All Liabilities</option>
                    <option value="paid" {{ request('filter_status') == 'paid' ? 'selected' : '' }}>Only Paid</option>
                    <option value="hide_paid" {{ request('filter_status') == 'hide_paid' ? 'selected' : '' }}>Hide Paid</option>
                </select>
            </div>

            <!-- Search Field -->
            <div class="col-md-4">
                <label for="search" class="form-label">Search Liabilities:</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Enter search term" value="{{ request('search') }}">
            </div>

            <!-- Submit Button for Search (optional if you want explicit submit) -->
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-secondary w-100">Search</button>
            </div>
        </div>
    </form>

    <div class="row">
        @foreach ($liabilities as $liability)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Liability ID: {{ $liability->id }}</span>
                        <!-- Conditionally show "Pay" or "View" button based on the status -->
                        @if($liability->status == 'paid')
                            <a href="{{ route('liabilities.show', $liability) }}" class="btn btn-sm btn-secondary">View</a>
                        @else
                            <a href="{{ route('liabilities.pay', $liability) }}" class="btn btn-sm btn-secondary">Pay</a>
                        @endif
                    </div>
                    <div class="card-body">
                        <p class="card-text"><strong>Name:</strong> {{ $liability->name }}</p>
                        <p class="card-text"><strong>Value:</strong> ${{ number_format($liability->amount, 2) }}</p>
                        <p class="card-text"><strong>Due Date:</strong> {{ $liability->due_date ?? 'N/A' }}</p>
                        <p class="card-text"><strong>Status:</strong>
                            <span class="badge
                                {{ $liability->status == 'paid' ? 'bg-success' : ($liability->status == 'overdue' ? 'bg-danger' : 'bg-warning') }}">
                                {{ ucfirst($liability->status) }}
                            </span>
                        </p>
                        <!-- Notes have been removed from the display -->
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $liabilities->appends([
            'account_id' => $selectedAccount,
            'filter_status' => request('filter_status'),
            'search' => request('search')
        ])->links() }}
    </div>
</div>
@endsection
