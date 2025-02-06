@extends('welcome')

@section('content')
<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Your Equities</h2>
        <a href="{{ route('equities.create') }}" class="btn btn-primary">Add an Equity</a>
    </div>

    <!-- Account Selection, Status Filter, and Search Form -->
    <form method="GET" action="{{ route('equities.index') }}">
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

            <!-- Equity Filter -->
            <div class="col-md-3">
                <label for="filter_status" class="form-label">Equity Filter:</label>
                <select name="filter_status" id="filter_status" class="form-control" onchange="this.form.submit()">
                    <option value="all" {{ request('filter_status', 'all') == 'all' ? 'selected' : '' }}>All Equities</option>
                    <option value="active" {{ request('filter_status') == 'active' ? 'selected' : '' }}>Active Equities</option>
                    <option value="inactive" {{ request('filter_status') == 'inactive' ? 'selected' : '' }}>Inactive Equities</option>
                </select>
            </div>

            <!-- Search Field -->
            <div class="col-md-4">
                <label for="search" class="form-label">Search Equities:</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Enter search term" value="{{ request('search') }}">
            </div>

            <!-- Submit Button (optional, since select fields auto-submit) -->
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-secondary w-100">Search</button>
            </div>
        </div>
    </form>

    <div class="row">
        @foreach ($equities as $equity)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Equity ID: {{ $equity->id }}</span>
                        <a href="{{ route('equities.show', $equity) }}" class="btn btn-sm btn-secondary">View</a>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><strong>Name:</strong> {{ $equity->name }}</p>
                        <p class="card-text"><strong>Symbol:</strong> {{ $equity->symbol }}</p>
                        <p class="card-text"><strong>Current Price:</strong> ${{ number_format($equity->current_price, 2) }}</p>
                        <p class="card-text"><strong>Quantity:</strong> {{ $equity->quantity }}</p>
                        <p class="card-text"><strong>Total Value:</strong> ${{ number_format($equity->amount, 2) }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $equities->appends([
            'account_id' => $selectedAccount,
            'filter_status' => request('filter_status'),
            'search' => request('search')
        ])->links() }}
    </div>
</div>
@endsection
