@extends('welcome')

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Liability Details: {{ $liability->name }}</h4>
        </div>
        <div class="card-body">
            <p class="mb-2"><strong>Liability ID:</strong> {{ $liability->id }}</p>
            <p class="mb-2"><strong>Account:</strong> {{ $liability->account->name ?? 'N/A' }}</p>
            <p class="mb-2"><strong>Amount:</strong> ${{ number_format($liability->amount, 2) }}</p>
            <p class="mb-2"><strong>Paid Amount:</strong> ${{ number_format($liability->paid_amount ?? 0, 2) }}</p>
            @if($liability->due_date)
                <p class="mb-2"><strong>Due Date:</strong> {{ $liability->due_date }}</p>
            @endif
            @if($liability->paid_at)
                <p class="mb-2"><strong>Paid At:</strong> {{ $liability->paid_at }}</p>
            @endif
            <p class="mb-2">
                <strong>Status:</strong>
                <span class="badge
                    {{ $liability->status == 'paid' ? 'bg-success' : ($liability->status == 'overdue' ? 'bg-danger' : 'bg-warning') }}">
                    {{ ucfirst($liability->status) }}
                </span>
            </p>
            <p class="mb-0"><strong>Description:</strong> {{ $liability->description ?? 'No description provided.' }}</p>
        </div>
    </div>

    <!-- Back Button -->
    <div class="mt-4">
        <a href="{{ route('liabilities.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i> Back to Liabilities
        </a>
    </div>
</div>
@endsection
