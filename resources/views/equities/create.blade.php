@extends('welcome')

@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('equities.store') }}" method="POST" class="mt-4">
    <div class="container-md mx-auto" style="max-width: 800px;">
        <h2 class="mb-4 text-center">Add New Equity</h2>
        @csrf

        <!-- Account Selection Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Select the account where you want to add this equity.
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
            <!-- Equity Name -->
            <div class="col-12">
                <div class="form-floating">
                    <input type="text" class="form-control" id="name" name="name" placeholder="Equity Name" required>
                    <label for="name">Equity Name</label>
                </div>
            </div>

            <!-- Ticker Symbol -->
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="text" class="form-control" id="symbol" name="symbol" placeholder="Ticker Symbol" required>
                    <label for="symbol">Ticker Symbol</label>
                </div>
            </div>

            <!-- Purchase Price -->
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="number" step="0.00000001" class="form-control" id="purchase_price" name="purchase_price" placeholder="Purchase Price" required>
                    <label for="purchase_price">Purchase Price ($)</label>
                </div>
            </div>

            <!-- Current Price -->
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="number" step="0.00000001" class="form-control" id="current_price" name="current_price" placeholder="Current Price">
                    <label for="current_price">Current Price ($)</label>
                </div>
            </div>

            <!-- Quantity -->
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="number" class="form-control" id="quantity" name="quantity" placeholder="Quantity" required>
                    <label for="quantity">Quantity</label>
                </div>
            </div>

            <!-- Currency -->
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="text" class="form-control" id="currency" name="currency" placeholder="Currency" value="USD" required>
                    <label for="currency">Currency (3-letter code)</label>
                </div>
            </div>

            <!-- Sector -->
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="text" class="form-control" id="sector" name="sector" placeholder="Sector">
                    <label for="sector">Sector</label>
                </div>
            </div>

            <!-- Dividends Received -->
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="number" step="0.00000001" class="form-control" id="dividends_received" name="dividends_received" placeholder="Dividends Received" value="0">
                    <label for="dividends_received">Dividends Received ($)</label>
                </div>
            </div>

            <!-- Purchase Date -->
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="datetime-local" class="form-control" id="purchased_at" name="purchased_at">
                    <label for="purchased_at">Purchase Date</label>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="col-12 mt-4">
                <div class="d-grid gap-2 col-md-6 mx-auto">
                    <button type="submit" class="btn btn-primary btn-lg py-2 fw-bold">
                        <i class="bi bi-plus-circle me-2"></i>Add Equity
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
