@extends('welcome')

@section('content')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('assets.store') }}" method="POST" class="mt-4">
    <div class="container-md mx-auto" style="max-width: 800px;">
        <h2 class="mb-4 text-center">Add New Asset</h2>
        @csrf

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
            <div class="col-12">
                <div class="form-floating">
                    <input type="text" class="form-control" id="name" name="name" placeholder="Asset Name" required>
                    <label for="name">Asset Name</label>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-floating">
                    <input type="text" class="form-control" id="location" name="location" placeholder="" required>
                    <label for="location">Location</label>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-floating">
                    <input type="datetime-local" class="form-control" id="purchase_date" name="purchase_date" placeholder="" required>
                    <label for="purchase_date">Purchase Date</label>
                </div>
            </div>

           <div class="col-md-6">
                <div class="form-floating">
                    <input type="number" class="form-control" id="current_value" name="current_value" step="0.01" placeholder="" required>
                    <label for="current_value">Current Value ($)</label>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-floating">
                    <input type="number" class="form-control" id="purchase_price" name="purchase_price" step="0.01" placeholder="" required>
                    <label for="purchase_price">Purchase Price ($)</label>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-floating">
                    <select class="form-select" id="category" name="category" required>
                        <option value="">Choose a category</option>
                        @foreach($assetCategories as $cat)
                        <option value="{{$cat->id}}">{{$cat->name}}</option>
                        @endforeach
                    </select>
                    <label for="category">Category</label>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-floating">
                    <select class="form-select" id="status" name="status" required>
                        <option value="">Choose a status</option>
                        @foreach($assetStatuses as $status)
                        <option value="{{$status->id}}">{{$status->name}}</option>
                        @endforeach
                    </select>
                    <label for="status">Status</label>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-floating">
                    <select class="form-select" id="type" name="type" required>
                        <option value="">Choose a type</option>
                        @foreach ($assetTypes as $type)
                            <option value="{{$type->id}}">{{$type->name}}</option>
                        @endforeach
                    </select>
                    <label for="type">Asset Type</label>
                </div>
            </div>

            <div class="col-md-6" id="sold-field" style="display: none;">
                <div class="form-floating">
                    <input type="text" class="form-control" id="sold_for" name="sold_for" placeholder="Sold Details">
                    <label for="sold_for">Sold Price</label>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-floating">
                    <select class="form-select" id="currency" name="currency" required>
                        <option value="">Choose a currency</option>
                        @foreach($currencies as $curr)
                        <option value="{{$curr->id}}">{{$curr->name}}</option>
                        @endforeach
                    </select>
                    <label for="currency">Currency</label>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-floating">
                    <input type="number" id="quantity" name="quantity" class="form-control" placeholder="" required>
                    <label for="quantity">Quantity</label>
                </div>
            </div>

            <div class="col-12">
                <div class="form-floating">
                    <textarea class="form-control" id="notes" name="notes" style="height: 100px" placeholder="" required></textarea>
                    <label for="notes">Additional Notes</label>
                </div>
            </div>

            <div class="col-12 mt-4 mb-5">
                <div class="d-grid gap-2 col-md-6 mx-auto">
                    <button type="submit" class="btn btn-primary btn-lg py-2 fw-bold">
                        <i class="bi bi-plus-circle me-2"></i>Add Asset
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelect = document.getElementById('status');
        const soldField = document.getElementById('sold-field');
        toggleSoldField();
        statusSelect.addEventListener('change', toggleSoldField);

        function toggleSoldField() {
            const selectedText = statusSelect.options[statusSelect.selectedIndex].text.trim();
            if (selectedText === 'Sold') {
                soldField.style.display = 'block';
            } else {
                soldField.style.display = 'none';
            }
        }
    });
    </script>

@endsection
