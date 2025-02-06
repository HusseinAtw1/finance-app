@extends('welcome')

@section('content')
<div class="container mt-5">
    <div class="row">
        <!-- Liability Details Card -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Pay Liability: {{ $liability->name }}</h4>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Liability ID:</strong> {{ $liability->id }}</p>
                    <p class="mb-2"><strong>Account:</strong> {{ $liability->account->name ?? 'N/A' }}</p>
                    <p class="mb-2"><strong>Amount Due:</strong> ${{ number_format($liability->amount, 2) }}</p>
                    @if($liability->due_date)
                        <p class="mb-2"><strong>Due Date:</strong> {{ $liability->due_date }}</p>
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
        </div>

        <!-- Payment Form Card -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Payment Details</h5>
                </div>
                <div class="card-body">
                    <!-- Display Validation Errors -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('liabilities.pay.update', $liability->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Paid Amount Field -->
                        <div class="mb-3">
                            <label for="paid_amount" class="form-label">Paid Amount ($)</label>
                            <input type="number" name="paid_amount" id="paid_amount" step="0.01"
                                   class="form-control @error('paid_amount') is-invalid @enderror" placeholder="Enter the amount you're paying" required>
                            @error('paid_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                You may pay a partial amount. The liability will remain pending until fully paid.
                            </div>
                        </div>

                        <!-- Paid At Field -->
                        <div class="mb-3">
                            <label for="paid_at" class="form-label">Paid At</label>
                            <input type="datetime-local" name="paid_at" id="paid_at"
                                   class="form-control @error('paid_at') is-invalid @enderror" required>
                            @error('paid_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Additional Liability Info (Read Only) -->
                        <div class="mb-3">
                            <label class="form-label">Total Liability Amount</label>
                            <input type="text" class="form-control" value="${{ number_format($liability->amount, 2) }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Current Paid Amount</label>
                            <input type="text" class="form-control" value="${{ number_format($liability->paid_amount ?? 0, 2) }}" readonly>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-check-circle me-2"></i>Submit Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional: Instructions or Note -->
    <div class="row">
        <div class="col">
            <div class="alert alert-info" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                Please review the liability details before submitting your payment.
            </div>
        </div>
    </div>
</div>
@endsection
