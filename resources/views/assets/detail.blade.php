@extends('welcome')

@section('content')
<div class="container my-5">
    <div class="card shadow-lg mb-5">
        <div class="card-header bg-primary text-white py-3">
            <h2 class="h4 mb-0"><i class="fas fa-cube me-2"></i>Asset Details - {{ $asset->name }}</h2>
        </div>
        <div class="card-body">
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="row g-3">
                        <div class="col-12">
                            <h5 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Basic Information</h5>
                            <dl class="row mb-0">
                                <dt class="col-sm-4">Account</dt>
                                <dd class="col-sm-8">{{ $asset->account->name }}</dd>

                                <dt class="col-sm-4">Currency</dt>
                                <dd class="col-sm-8">{{ optional($asset->currency)->symbol }} ({{ optional($asset->currency)->name }})</dd>

                                <dt class="col-sm-4">Asset Type</dt>
                                <dd class="col-sm-8">{{ $asset->assetType->name }}</dd>

                                <dt class="col-sm-4">Category</dt>
                                <dd class="col-sm-8">{{ $asset->assetCategory->name }}</dd>

                                <dt class="col-sm-4">Status</dt>
                                <dd class="col-sm-8">
                                    <span class="badge bg-success">{{ $asset->assetStatus->name }}</span>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="row g-3">
                        <div class="col-12">
                            <h5 class="text-primary mb-3"><i class="fas fa-chart-line me-2"></i>Financial Details</h5>
                            <dl class="row mb-0">
                                <dt class="col-sm-4">Purchase Date</dt>
                                <dd class="col-sm-8">{{ $asset->purchase_at }} UTC</dd>

                                <dt class="col-sm-4">Quantity</dt>
                                <dd class="col-sm-8">{{ $asset->quantity }}</dd>

                                <dt class="col-sm-4">Current Value</dt>
                                <dd class="col-sm-8 text-success fw-bold">{{ $asset->current_value }}</dd>

                                <dt class="col-sm-4">Purchase Price</dt>
                                <dd class="col-sm-8">{{ $asset->purchase_price }}</dd>

                                <dt class="col-sm-4">Location</dt>
                                <dd class="col-sm-8">{{ $asset->location }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-top pt-4">
                <h5 class="text-primary mb-3"><i class="fas fa-sticky-note me-2"></i>Additional Notes</h5>
                <div class="bg-light p-3 rounded">
                    {{ $asset->notes ?? 'No additional notes provided' }}
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-lg">
        <div class="card-header bg-primary py-3">
            <h2 class="h4 mb-0" style="color: white"><i class="fas fa-hand-holding-usd me-2"></i>Sell Asset</h2>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <h5 class="alert-heading">Please fix the following errors:</h5>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('assets.sell', $asset->id) }}" method="POST" class="needs-validation" novalidate>
                @csrf
                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="sell_price" class="form-label">Selling Price <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">{{ optional($asset->currency)->symbol ?? '$' }}</span>
                            <input type="number" step="0.01" id="sell_price" name="sell_price"
                                   class="form-control @error('sell_price') is-invalid @enderror"
                                   placeholder="Enter selling price" value="{{ old('sell_price') }}" required>
                            @error('sell_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="sold_at" class="form-label">Sale Date <span class="text-danger">*</span></label>
                        <input type="datetime-local" id="sold_at" name="sold_at"
                               class="form-control @error('sold_at') is-invalid @enderror"
                               value="{{ old('sold_at') }}" required>
                        @error('sold_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <input type="hidden" name="account_id" value="{{ $asset->account_id }}">

                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </a>
                            <button type="submit" class="btn btn-danger px-4">
                                <i class="fas fa-check-circle me-2"></i>Confirm Sale
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 15px;
    }
    .card-header {
        border-radius: 15px 15px 0 0 !important;
    }
    dt {
        font-weight: 500;
        color: #6c757d;
    }
    dd {
        color: #333;
    }
    .form-label {
        font-weight: 500;
    }
    .input-group-text {
        background-color: #e9ecef;
    }
</style>
@endsection
