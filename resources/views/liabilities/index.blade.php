@extends('welcome')

@section('content')
<div class="container py-4">
    <!-- Header with title and add button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">Liabilities</h1>
        <a href="#" class="btn btn-primary">
            <i class="bi bi-plus me-1"></i> Add Liability
        </a>
    </div>

    <!-- Search and filter form -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form action="{{ route('liabilities.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-lg-6 col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name">
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-funnel me-1"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liabilities cards -->
    <div class="row g-4">
        @forelse($liabilities as $liability)
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0 hover-shadow">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 text-truncate">{{ $liability->name }}</h5>
                        <span class="badge bg-primary rounded-pill">ID: {{ $liability->id }}</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Status</span>
                                <span class="fw-bold text-capitalize">{{ $liability->status }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Amount</span>
                                <span class="fw-bold">{{ number_format($liability->amount, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Paid</span>
                                <span class="fw-bold">{{ number_format($liability->paid_amount, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Due Date</span>
                                <span class="fw-bold">
                                    {{ $liability->due_date ? \Carbon\Carbon::parse($liability->due_date)->format('M d, Y') : 'N/A' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 p-3">
                        <a href="#" class="btn btn-outline-primary w-100">
                            <i class="bi bi-eye me-2"></i>View Details
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <!-- Message if no liabilities found -->
            <div class="col-12">
                <div class="alert alert-info d-flex align-items-center shadow-sm">
                    <i class="bi bi-info-circle-fill me-2 fs-4"></i>
                    <div>No liabilities found. Try adjusting your search filters.</div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination links -->
    <div class="d-flex justify-content-center mt-4">
        {{ $liabilities->links() }}
    </div>
</div>

<style>
    body {
        background-color: #e8e8e8;
    }
</style>
@endsection
