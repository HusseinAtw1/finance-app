@extends('welcome')

@section('content')

<div class="container my-5">
    <x-asset-details :asset="$asset"/>
    @if ($asset->assetStatus->name !== 'Sold')
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
                                <span class="input-group-text" id="currencySymbol">{{ optional($asset->currency)->symbol ?? '$' }}</span>
                                <input type="number" step="0.01" id="sell_price" name="sell_price" class="form-control @error('sell_price') is-invalid @enderror" placeholder="Enter selling price" value="{{ old('sell_price') }}" required>
                                @error('sell_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="sold_at" class="form-label">Sale Date <span class="text-danger">*</span></label>
                            <input type="datetime-local" id="sold_at" name="sold_at"class="form-control @error('sold_at') is-invalid @enderror" value="{{ old('sold_at') }}" required>
                            @error('sold_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="currency" class="form-label">Currency <span class="text-danger">*</span></label>
                            <select name="currency" id="currency" class="form-select">
                                <option value="" data-symbol="$" selected>Choose a Currency</option>
                                @foreach ($currencies as $currency)
                                    <option value="{{ $currency->id }}" data-symbol="{{ $currency->symbol }}">
                                        {{ $currency->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('currency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="col-md-6">
                            <label for="quantity" class="form-label">Quantity Sold <span class="text-danger">*</span></label>
                            <input type="number" id="quantity" name="quantity"
                                class="form-control @error('quantity') is-invalid @enderror"
                                value="{{ old('quantity') }}" required>
                            @error('quantity')
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
    @endif
</div>

@endsection

@section('scripts')
<script>

    document.addEventListener('DOMContentLoaded', function () {
        const currencySelect = document.getElementById('currency');
        const currencySymbol = document.getElementById('currencySymbol');

        currencySelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const symbol = selectedOption.getAttribute('data-symbol') || '$';
            currencySymbol.textContent = symbol;
        });
    });
</script>
@endsection
