@extends('welcome')

@section('content')
<div class="container-md mx-auto" style="max-width: 800px;">
    <h2 class="mb-4 text-center">Add New Liability</h2>

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

    <form action="{{ route('liabilities.store') }}" method="POST" class="mt-4">
        @csrf

        <!-- Account Selection Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Select the account where you want to add this liability
                </div>
                <div class="form-floating">
                    <select name="account_id" id="account_id" class="form-select @error('account_id') is-invalid @enderror" required>
                        <option value="">Choose an account</option>
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}" {{ old('account_id') == $account->id ? 'selected' : '' }}>
                                {{ $account->name }}
                            </option>
                        @endforeach
                    </select>
                    <label for="account_id">Select Account</label>
                    @error('account_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row g-3">
            <!-- Liability Name -->
            <div class="col-12">
                <div class="form-floating">
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Liability Name" value="{{ old('name') }}" required>
                    <label for="name">Liability Name</label>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div class="col-12">
                <div class="form-floating">
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" placeholder="Description" style="height: 100px">{{ old('description') }}</textarea>
                    <label for="description">Description</label>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Amount -->
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" placeholder="Amount" step="0.01" value="{{ old('amount') }}" required>
                    <label for="amount">Amount ($)</label>
                    @error('amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Due Date -->
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="datetime-local" class="form-control @error('due_date') is-invalid @enderror" id="due_date" name="due_date" placeholder="Due Date" value="{{ old('due_date') }}">
                    <label for="due_date">Due Date & Time</label>
                    @error('due_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Status -->
            <div class="col-md-6">
                <div class="form-floating">
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                        <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ old('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="overdue" {{ old('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    </select>
                    <label for="status">Status</label>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Paid At -->
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="datetime-local" class="form-control @error('paid_at') is-invalid @enderror" id="paid_at" name="paid_at" placeholder="Paid At" value="{{ old('paid_at') }}">
                    <label for="paid_at">Paid At</label>
                    @error('paid_at')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Paid Amount -->
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="number" class="form-control @error('paid_amount') is-invalid @enderror" id="paid_amount" name="paid_amount" placeholder="Paid Amount" step="0.01" value="{{ old('paid_amount') }}">
                    <label for="paid_amount">Paid Amount ($)</label>
                    @error('paid_amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Submit Button -->
            <div class="col-12 mt-4">
                <div class="d-grid gap-2 col-md-6 mx-auto">
                    <button type="submit" class="btn btn-primary btn-lg py-2 fw-bold">
                        <i class="bi bi-plus-circle me-2"></i>Add Liability
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
