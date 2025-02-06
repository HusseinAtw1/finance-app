@extends('welcome')

@section('content')
<div class="container my-5">
    <!-- Asset Details Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h1 class="mb-0">Asset Details - {{ $asset->name }}</h1>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <strong>Type:</strong>
                    <p>{{ $asset->type }}</p>
                </div>
                <div class="col-md-4">
                    <strong>Current Value:</strong>
                    <p>${{ number_format($asset->current_value, 2) }}</p>
                </div>
                <div class="col-md-4">
                    <strong>Purchase Price:</strong>
                    <p>${{ number_format($asset->purchase_price, 2) }}</p>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <strong>Purchase Date:</strong>
                    <p>{{ $asset->purchase_date }}</p>
                </div>
                <div class="col-md-4">
                    <strong>Category:</strong>
                    <p>{{ $asset->category }}</p>
                </div>
                <div class="col-md-4">
                    <strong>Location:</strong>
                    <p>{{ $asset->location }}</p>
                </div>
            </div>
            <div class="mb-3">
                <strong>Notes:</strong>
                <p>{{ $asset->notes }}</p>
            </div>
        </div>
    </div>

    <!-- Sell Asset Card -->
    <div class="card">
        <div class="card-header">
            <h2 class="mb-0">Sell Asset</h2>
        </div>
        <div class="card-body">
            <!-- Global Error Summary -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('assets.sell', $asset->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="sell_price" class="form-label">Selling Price:</label>
                    <input type="number" step="0.01" id="sell_price" name="sell_price" required placeholder="Enter selling price" class="form-control" value="{{ old('sell_price') }}">
                    @error('sell_price')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="sold_at" class="form-label">Sale Date:</label>
                    <input type="date" id="sold_at" name="sold_at" required max="{{ now()->toDateString() }}" class="form-control" value="{{ old('sold_at') }}">
                    @error('sold_at')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <input type="hidden" name="account_id" value="{{ $asset->account_id }}">
                <div class="d-flex justify-content-between">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">&larr; Back</a>
                    <button type="submit" class="btn btn-danger">Confirm Sale</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
