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

<div class="container my-5">
    <!-- Equity Details Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h1 class="mb-0">Equity Details - {{ $equity->name ?? 'N/A' }}</h1>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- First Column -->
                <div class="col-md-4">
                    <p class="text-muted mb-1">Symbol:</p>
                    <p class="fw-bold">{{ $equity->symbol ?? 'N/A' }}</p>

                    <p class="text-muted mb-1">Quantity:</p>
                    <p class="fw-bold">{{ $equity->quantity ?? 'N/A' }}</p>

                    <p class="text-muted mb-1">Sector:</p>
                    <p class="fw-bold">{{ $equity->sector ?? 'N/A' }}</p>

                    <p class="text-muted mb-1">Purchase Date:</p>
                    <p class="fw-bold">
                        @if($equity->purchased_at)
                            {{ \Carbon\Carbon::parse($equity->purchased_at)->format('Y-m-d H:i') }}
                        @else
                            N/A
                        @endif
                    </p>
                </div>

                <!-- Second Column -->
                <div class="col-md-4">
                    <p class="text-muted mb-1">Purchase Price:</p>
                    <p class="fw-bold">${{ number_format($equity->purchase_price, 8) }}</p>

                    <p class="text-muted mb-1">Total Value:</p>
                    <p class="fw-bold">${{ number_format($equity->amount, 8) }}</p>

                    <p class="text-muted mb-1">Dividends Received:</p>
                    <p class="fw-bold">${{ number_format($equity->dividends_received, 8) }}</p>
                </div>

                <!-- Third Column -->
                <div class="col-md-4">
                    <p class="text-muted mb-1">Current Price:</p>
                    <p class="fw-bold">
                        @if($equity->current_price)
                            ${{ number_format($equity->current_price, 8) }}
                        @else
                            N/A
                        @endif
                    </p>

                    <p class="text-muted mb-1">Currency:</p>
                    <p class="fw-bold">{{ $equity->currency ?? 'N/A' }}</p>

                    <p class="text-muted mb-1">Status:</p>
                    <p class="fw-bold">{{ $equity->status ?? 'active' }}</p>

                    <p class="text-muted mb-1">Sold Date:</p>
                    <p class="fw-bold">
                        @if($equity->sold_at)
                            {{ \Carbon\Carbon::parse($equity->sold_at)->format('Y-m-d H:i') }}
                        @else
                            Not Sold
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sell Equity Card -->
    <div class="card">
        <div class="card-header">
            <h2 class="mb-0">Sell Equity</h2>
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

            <form action="{{ route('equities.sell', $equity->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="shares" class="form-label">Number of Shares to Sell:</label>
                    <input type="number" min="1" max="{{ $equity->quantity }}" id="shares" name="shares" required
                           placeholder="Enter number of shares" class="form-control" value="{{ old('shares') }}">
                    @error('shares')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="sell_price" class="form-label">Selling Price (per share):</label>
                    <input type="number" step="0.00000001" id="sell_price" name="sell_price" required
                           placeholder="Enter selling price per share" class="form-control" value="{{ old('sell_price') }}">
                    @error('sell_price')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="sold_at" class="form-label">Sale Date:</label>
                    <input type="datetime-local" id="sold_at" name="sold_at" required
                           max="{{ now()->toDateString() }}" class="form-control" value="{{ old('sold_at') }}">
                    @error('sold_at')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <!-- Optionally include the account_id as a hidden field if needed -->
                <input type="hidden" name="account_id" value="{{ $equity->account_id }}">
                <div class="d-flex justify-content-between">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">&larr; Back</a>
                    <button type="submit" class="btn btn-danger">Confirm Sale</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
