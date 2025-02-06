@extends('welcome')

@section('content')
<form action="{{ route('assets.store') }}" method="POST" class="mt-4">
    <div class="container-md mx-auto" style="max-width: 800px;">
        <h2 class="mb-4 text-center">Add New Asset</h2>
        @csrf

        <!-- Account Selection Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Select the account where you want to add this asset
                </div>
                <div class="form-floating">
                    <select name="account_id" id="account_id" class="form-select" required>
                        <option value="">Choose an account</option>
                        @foreach ($accs as $account)
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
            <!-- Asset Name -->
            <div class="col-12">
                <div class="form-floating">
                    <input type="text" class="form-control" id="name" name="name" placeholder="Asset Name" required>
                    <label for="name">Asset Name</label>
                </div>
            </div>

            <!-- Type + Current Value -->
            <div class="col-md-6">
                <div class="form-floating">
                    <select class="form-select" id="type" name="type" required>
                        <option value="cash">Cash</option>
                        <option value="investment">Investment</option>
                        <option value="property">Property</option>
                    </select>
                    <label for="type">Asset Type</label>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-floating">
                    <input type="number" class="form-control" id="current_value" name="current_value" step="0.01" required>
                    <label for="current_value">Current Value ($)</label>
                </div>
            </div>

            <!-- Purchase Info -->
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="datetime-local" class="form-control" id="purchase_date" name="purchase_date">
                    <label for="purchase_date">Purchase Date</label>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-floating">
                    <input type="number" class="form-control" id="purchase_price" name="purchase_price" step="0.01">
                    <label for="purchase_price">Purchase Price ($)</label>
                </div>
            </div>

            <!-- Category + Location -->
            <div class="col-md-6">
                <div class="form-floating">
                    <select class="form-select" id="category" name="category">
                        <option value="fixed">Fixed</option>
                        <option value="liquid">Liquid</option>
                    </select>
                    <label for="category">Category</label>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-floating">
                    <input type="text" class="form-control" id="location" name="location">
                    <label for="location">Location</label>
                </div>
            </div>

            <!-- Notes -->
            <div class="col-12">
                <div class="form-floating">
                    <textarea class="form-control" id="notes" name="notes" style="height: 100px"></textarea>
                    <label for="notes">Additional Notes</label>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="col-12 mt-4">
                <div class="d-grid gap-2 col-md-6 mx-auto">
                    <button type="submit" class="btn btn-primary btn-lg py-2 fw-bold">
                        <i class="bi bi-plus-circle me-2"></i>Add Asset
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
