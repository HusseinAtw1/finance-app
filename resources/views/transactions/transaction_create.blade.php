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
        <div class="container mt-5">
            <div class="row">
                <div class="col-md">
                    <div>
                        <h1 class="mb-4">Transaction Details</h1>
                    </div>
                    <div class="container mt-4">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Type</th>
                                        <th>Account</th>
                                        <th>Supplier</th>
                                        <th>Customer</th>
                                        <th>Type</th>
                                        <th>Current Price</th>
                                        <th>Purchase Price</th>
                                        <th>Sold For</th>
                                        <th>Quantity</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactionDetails as $detail)
                                        <tr>
                                            <td>{{ class_basename($detail->transactionable_type) }}</td>
                                            <td>{{ $detail->account->name }}</td>
                                            <td>{{ $detail->supplier->name ?? '-' }}</td>
                                            <td>{{ $detail->customer->name ?? null }} - {{ $detail->customer->phone_number ?? null }}</td>
                                            <td>{{ ucfirst($detail->type) }}</td>
                                            <td>{{ $detail->current_price }}</td>
                                            <td>{{ $detail->purchase_price }}</td>
                                            <td>{{ $detail->sold_for ?? '-' }}</td>
                                            <td>{{ $detail->quantity }}</td>
                                            <td>{{ $detail->amount }}</td>
                                            <td>
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="container mt-3">
                        <div class="d-flex justify-content-center gap-2 mb-4">
                            <button class="btn btn-primary" onclick="showAssetForm()">Add Asset</button>
                            <button class="btn btn-secondary" onclick="showLiabilityForm()">Add Liability</button>
                            <button class="btn btn-info" onclick="showExpenseForm()">Add Expense</button>
                            <button class="btn btn-outline-dark" onclick="hideAllForms()">Hide Forms</button>
                        </div>
                    </div>

                    <div id="assetTemplate" class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h2><span id="action"></span> Asset</h2>
                            <div>
                                <button class="btn btn-success me-2" onclick="buyAssetForm()">Buy</button>
                                <button class="btn btn-danger" onclick="sellAssetForm()">Sell</button>
                            </div>
                        </div>

                        <div class="card-body">
                            <form method="POST" action="{{ route('transaction_asset.store', ['transaction' => $transaction->id]) }}" id="assetForm">
                                @csrf
                                <input type="hidden" name="_method" value="POST" id="methodInput">
                                {{-- New Asset Details Fields --}}
                                <div class="row">
                                    <div class="col-md-3 mb-3" id ='nameDiv'>
                                        <label for="name" class="form-label">Name:</label>
                                        <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control" required>
                                        @error('name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 mb-3" id='referenceNumberDiv'>
                                        <label for="reference_number" class="form-label">Reference Number:</label>
                                        <input type="text" name="reference_number" id="reference_number" value="{{ old('reference_number') }}" class="form-control" required>
                                        @error('reference_number')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 mb-3" id="assetCategoryDiv">
                                        <label for="asset_category" class="form-label">Select Category:</label>
                                        <select name="asset_category" id="asset_category" class="form-select" required>
                                            <option value="">--Select Category--</option>
                                            @foreach($assetCategories as $category)
                                                <option value="{{ $category->id }}" {{ old('asset_category') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('asset_category')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 mb-3" id="assetTypeDiv">
                                        <label for="asset_type" class="form-label">Select Type:</label>
                                        <select name="asset_type" id="asset_type" class="form-select" required>
                                            <option value="">--Select Type--</option>
                                            @foreach($assetTypes as $type)
                                                <option value="{{ $type->id }}" {{ old('asset_type') == $type->id ? 'selected' : '' }}>
                                                    {{ $type->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('asset_type')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">

                                    <div class="col-md-3 mb-3" id="assetDiv">
                                        <label for="asset_id" class="form-label">Select Asset:</label>
                                        <select name="asset_id" id="asset_id" class="form-select" required>
                                            <option value="">--Select Asset--</option>
                                            @foreach($assets as $asset)
                                                <option value="{{ $asset->id }}">{{ $asset->name.'-'.$asset->reference_number }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3 mb-3" id="accountDiv">
                                        <label for="account_id" class="form-label">Select Account:</label>
                                        <select name="account_id" id="account_id" class="form-select" required>
                                            <option value="">--Select Account--</option>
                                            @foreach($accounts as $acc)
                                                <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3" id="currencyDiv">
                                        <label for="currency" class="form-label">Currency:</label>
                                        <select name="currency_id" id="currency" class="form-select" required>
                                            <option value="">--Select Currency--</option>
                                            @foreach($currencies as $currency)
                                                <option value="{{ $currency->id }}" {{ old('currency_id') == $currency->id ? 'selected' : '' }}>
                                                    {{ $currency->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('currency_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 mb-3" id="depreciationDiv">
                                        <label for="depreciation" class="form-label">Depreciation Method:</label>
                                        <select name="depreciation_id" id="depreciation" class="form-select" required>
                                            <option value="">--Select Depreciation--</option>
                                            @foreach($depreciations as $depreciation)
                                                <option value="{{ $depreciation->id }}" {{ old('depreciation_id') == $depreciation->id ? 'selected' : '' }}>
                                                    {{ $depreciation->method }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('depreciation_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 mb-3" id="transactionTypeDiv">
                                        <label for="transaction_type" class="form-label">Transaction Type:</label>
                                        <select name="transaction_type" id="transaction_type" class="form-select" onchange="handleTransactionType(this)" required disabled>
                                            <option value="" id="transaction_type_option"></option>
                                        </select>
                                        @error('transaction_type')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 mb-3" id="sellDateDiv" style="display: none">
                                        <label for="sold_at" class="form-label">Sell Date:</label>
                                        <input type="datetime-local" name="sold_at" id="sold_at" value="{{ old('sold_at') }}" class="form-control" required>
                                        @error('sold_at')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-md-3 mb-3"  id="quantityDiv">
                                        <label for="quantity" class="form-label">Quantity:</label>
                                        <input type="number" name="quantity" id="quantity" step="1" min="0" value="{{ old('quantity') }}" class="form-control" required>
                                        @error('quantity')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 mb-3" style="display: none" id="customerNameDiv">
                                        <label for="customer" class="form-label">Customer Name:</label>
                                        <input type="text" name="customer" id="customer" value="{{ old('customer') }}" class="form-control">
                                        @error('customer')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 mb-3" style="display: none" id="customerNumberDiv">
                                        <label for="customer_number" class="form-label">Customer Number:</label>
                                        <input type="text" name="customer_number" id="customer_number" value="{{ old('customer_number') }}" class="form-control">
                                        @error('customer_number')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 mb-3" style="display: none" id="soldForDiv">
                                        <label for="sold_for" class="form-label">Sold Price:</label>
                                        <input type="text" name="sold_for" id="sold_for" value="{{ old('sold_for') }}" class="form-control">
                                        @error('sold_for')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 mb-3" id="currentValueDiv">
                                        <label for="current_value" class="form-label">Current Value:</label>
                                        <input type="number" name="current_value" id="current_value" step="0.01" min="0" value="{{ old('current_value') }}" class="form-control" required>
                                        @error('current_value')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 mb-3" id="purchasePriceDiv">
                                        <label for="purchase_price" class="form-label">Purchase Price:</label>
                                        <input type="number" name="purchase_price" id="purchase_price" step="0.01" min="0" value="{{ old('purchase_price') }}" class="form-control" required>
                                        @error('purchase_price')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 mb-3" id="purchaseDateDiv">
                                        <label for="purchase_date" class="form-label">Purchase Date:</label>
                                        <input type="datetime-local" name="purchase_date" id="purchase_date" value="{{ old('purchase_date') }}" class="form-control" required>
                                        @error('purchase_date')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3 mb-3" id="locationDiv">
                                        <label for="location" class="form-label">Location:</label>
                                        <input type="text" name="location" id="location" value="{{ old('location') }}" class="form-control" required>
                                        @error('location')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 mb-3" style="display: none" id="supplierDiv">
                                        <label for="supplier_id" class="form-label">Supplier:</label>
                                        <select name="supplier_id" id="supplier_id" class="form-select">
                                            <option value="">--Select Supplier--</option>
                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                                    {{ $supplier->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('supplier_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-3" id="notesDiv">
                                        <label for="notes" class="form-label">Notes:</label>
                                        <textarea name="notes" id="notes" class="form-control" rows="3" required>{{ old('notes') }}</textarea>
                                        @error('notes')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">Add Asset to Transaction</button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @vite('resources/js/transaction_create.js')

    <script>
        window.storeAssetUrl = "{{ route('transaction_asset.store', $transaction->id) }}";
        window.sellAssetUrl = "{{ route('transaction_asset.sell', $transaction->id) }}";
    </script>

    @endsection
