@extends('welcome')

@section('content')
<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Your Assets</h2>
        <a href="{{ route('assets.create') }}" class="btn btn-primary">Add an Asset</a>
    </div>

    <!-- Account Selection, Asset Status Filter, and Search Form -->
    <form method="GET" action="{{ route('assets.show') }}">
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

            <!-- Asset Status Filter -->
            <div class="col-md-3">
                <label for="status" class="form-label">Asset Filter:</label>
                <select name="status" id="status" class="form-control" onchange="this.form.submit()">
                    <option value="owned" {{ request('status', 'owned') == 'owned' ? 'selected' : '' }}>Owned</option>
                    <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>Sold</option>
                </select>
            </div>

            <!-- Search Field -->
            <div class="col-md-4">
                <label for="search" class="form-label">Search Assets:</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Enter search term" value="{{ request('search') }}">
            </div>

            <!-- Submit Button (optional if you want an explicit submit) -->
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-secondary w-100">Search</button>
            </div>
        </div>
    </form>

    <div class="row">
        @forelse ($assets as $asset)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Asset ID: {{ $asset->id }}</span>
                        <a href="{{ route('assets_detials.show', $asset->id) }}" class="btn btn-primary btn-sm">View</a>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><strong>Value:</strong> ${{ $asset->current_value }}</p>
                        <p class="card-text"><strong>Date:</strong> {{ $asset->created_at->format('M d, Y') }}</p>
                        <p class="card-text"><strong>Notes:</strong> {{ $asset->notes }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p>No assets found with the selected filters.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $assets->appends(request()->query())->links() }}
    </div>
</div>
@endsection
