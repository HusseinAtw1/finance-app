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

<div class="container py-4">
    <h1 class="mb-4">Create New Transaction</h1>

    <form action="{{ route('transactions.store') }}" method="POST" id="transaction-form">
        @csrf

        <!-- Currency Selection (for the whole transaction) -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Transaction Currency</h5>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="currency_id">Select Currency</label>
                    <select name="currency_id" id="currency_id" class="form-control" required>
                        <option value="">-- Select Currency --</option>
                        @foreach($currencies as $currency)
                            <option value="{{ $currency->id }}">{{ $currency->name }} ({{ $currency->symbol }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Transaction Details -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Transaction Details</h5>
            </div>
            <div class="card-body">
                <div class="form-group mb-3">
                    <label for="transaction_date">Transaction Date</label>
                    <input type="datetime-local" name="transaction_date" id="transaction_date" class="form-control" required value="{{ date('Y-m-d\TH:i') }}">
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                </div>
            </div>
        </div>

        <!-- Transaction Items Section -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Transaction Items</h5>
                <button type="button" class="btn btn-success btn-sm" id="add-item-btn">
                    <i class="fas fa-plus"></i> Add Item
                </button>
            </div>
            <div class="card-body">
                <div id="items-container">
                    <!-- Dynamic items will be added here -->
                </div>

                <div class="alert alert-info mt-3" id="no-items-message">
                    No items added yet. Click the "Add Item" button to add transaction items.
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="text-end">
            <button type="submit" class="btn btn-primary" id="submit-btn" disabled>Create Transaction</button>
        </div>
    </form>
</div>

<!-- Template for new item (hidden) -->
<template id="item-template">
    <div class="item-row card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
                <h6 class="item-number">Item #1</h6>
                <button type="button" class="btn btn-danger btn-sm remove-item-btn">
                    <i class="fas fa-times"></i> Remove
                </button>
            </div>

            <!-- Financial Category - Asset Only -->
            <div class="form-group mb-3">
                <label>Financial Category</label>
                <select name="items[0][category]" class="form-control financial-category-select" required>
                    <option value="">-- Select Category --</option>
                    <option value="asset">Asset</option>
                </select>
            </div>

            <!-- Asset selection with Create New button -->
            <div class="form-group mb-3 asset-selection">
                <label>Select Asset</label>
                <div class="d-flex">
                    <select name="items[0][asset_id]" class="form-control asset-select">
                        <option value="">-- Select Asset --</option>
                        @foreach($assets as $asset)
                            <option value="{{ $asset->id }}">{{ $asset->name }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="btn btn-secondary ms-2 create-new-asset-btn" data-bs-toggle="collapse" data-bs-target="#newAssetCollapse_0" aria-expanded="false" aria-controls="newAssetCollapse_0">
                        <i class="fas fa-plus"></i> Create New Asset
                    </button>
                </div>

                <!-- New Asset Form (using Bootstrap collapse) -->
                <div class="collapse new-asset-form mt-3" id="newAssetCollapse_0">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Create New Asset</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Reference Number</label>
                                    <input type="text" name="items[0][new_asset][reference_number]" class="form-control new-asset-reference-number" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Name</label>
                                    <input type="text" name="items[0][new_asset][name]" class="form-control new-asset-name" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Asset Type</label>
                                    <select name="items[0][new_asset][asset_type_id]" class="form-control new-asset-type" required>
                                        <option value="">-- Select Type --</option>
                                        @foreach($assetTypes as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Asset Category</label>
                                    <select name="items[0][new_asset][asset_category_id]" class="form-control new-asset-category" required>
                                        <option value="">-- Select Category --</option>
                                        @foreach($assetCategories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Status</label>
                                    <select name="items[0][new_asset][asset_status_id]" class="form-control new-asset-status" required>
                                        <option value="">-- Select Status --</option>
                                        @foreach($assetStatuses as $status)
                                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Removed Location from here -->
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-danger cancel-new-asset-btn me-2">Cancel</button>
                                <button type="button" class="btn btn-success confirm-new-asset-btn">Confirm</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group mb-3">
                <label>Account</label>
                <select name="items[0][account_id]" class="form-control account-select" required>
                    <option value="">-- Select Account --</option>
                    @foreach($accounts as $account)
                        <option value="{{ $account->id }}">{{ $account->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Common fields for all categories -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Amount</label>
                    <input type="number" name="items[0][amount]" class="form-control amount-input" step="0.01" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Type</label>
                    <select name="items[0][type]" class="form-control type-select" required>
                        <option value="">-- Select Type --</option>
                        <option value="debit">Debit</option>
                        <option value="credit">Credit</option>
                    </select>
                </div>
            </div>


            <!-- Asset Purchase Details -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6>Asset Purchase Details</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Current Value</label>
                            <input type="number" name="items[0][current_value]" class="form-control current-value-input" step="0.01" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Purchase Price</label>
                            <input type="number" name="items[0][purchase_price]" class="form-control purchase-price-input" step="0.01">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Purchase Date</label>
                            <input type="datetime-local" name="items[0][purchase_at]" class="form-control purchase-date-input">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Quantity</label>
                            <input type="number" name="items[0][quantity]" class="form-control quantity-input" value="1" min="1">
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label>Location</label>
                        <input type="text" name="items[0][location]" class="form-control location-input">
                    </div>
                    <!-- Buyer selection appears only if the type is debit -->
                    <div class="form-group mb-3 buyer-selection-container" style="display: none;">
                        <label>Buyer</label>
                        <select name="items[0][buyer_id]" class="form-control buyer-select">
                            <option value="">-- Select Buyer --</option>
                            @foreach($buyers as $buyer)
                                <option value="{{ $buyer->id }}">{{ $buyer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>


            <!-- Notes field -->
            <div class="form-group mb-3">
                <label>Notes</label>
                <textarea name="items[0][notes]" class="form-control notes-input" rows="2"></textarea>
            </div>
        </div>
    </div>
</template>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const itemsContainer = document.getElementById('items-container');
        const addItemBtn = document.getElementById('add-item-btn');
        const noItemsMessage = document.getElementById('no-items-message');
        const submitBtn = document.getElementById('submit-btn');
        const itemTemplate = document.getElementById('item-template');
        const transactionCurrencySelect = document.getElementById('currency_id');

        let itemCount = 0;

        // Function to update item numbers and enable/disable submit button
        function updateItemNumbers() {
            const items = itemsContainer.querySelectorAll('.item-row');
            items.forEach((item, index) => {
                const itemNumber = item.querySelector('.item-number');
                itemNumber.textContent = `Item #${index + 1}`;

                // Update name attributes to maintain correct array indexing
                updateNameAttributes(item, index);
            });

            // Show/hide no items message
            if (items.length === 0) {
                noItemsMessage.style.display = 'block';
                submitBtn.disabled = true;
            } else {
                noItemsMessage.style.display = 'none';
                submitBtn.disabled = false;
            }
        }

        // Function to update name attributes and IDs for a specific item
        function updateNameAttributes(item, index) {
            // Update all input, select, and textarea elements with array index in their name
            const inputs = item.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                if (input.name && input.name.includes('[')) {
                    input.name = input.name.replace(/items\[\d+\]/, `items[${index}]`);
                }
            });

            // Update collapse ID for new asset form and corresponding button attributes
            const newAssetFormCollapse = item.querySelector('.new-asset-form');
            if (newAssetFormCollapse) {
                newAssetFormCollapse.id = `newAssetCollapse_${index}`;
            }
            const createNewAssetBtn = item.querySelector('.create-new-asset-btn');
            if (createNewAssetBtn) {
                createNewAssetBtn.setAttribute('data-bs-target', `#newAssetCollapse_${index}`);
                createNewAssetBtn.setAttribute('aria-controls', `newAssetCollapse_${index}`);
            }
        }

        // Function to setup new asset form interactions
        function setupNewAssetForm(item) {
            const newAssetForm = item.querySelector('.new-asset-form');
            const assetSelect = item.querySelector('.asset-select');
            const cancelNewAssetBtn = item.querySelector('.cancel-new-asset-btn');
            const confirmNewAssetBtn = item.querySelector('.confirm-new-asset-btn');

            // Cancel new asset creation
            cancelNewAssetBtn.addEventListener('click', function() {
                let collapseInstance = bootstrap.Collapse.getInstance(newAssetForm);
                if (!collapseInstance) {
                    collapseInstance = new bootstrap.Collapse(newAssetForm);
                }
                collapseInstance.hide();
                assetSelect.disabled = false;
                item.querySelectorAll('.new-asset-form input, .new-asset-form select, .new-asset-form textarea').forEach(el => {
                    if (el.tagName === 'SELECT') {
                        el.selectedIndex = 0;
                    } else {
                        el.value = '';
                    }
                });
            });

            // Confirm new asset creation
            confirmNewAssetBtn.addEventListener('click', function() {
                const requiredFields = item.querySelectorAll('.new-asset-form [required]');
                let isValid = true;
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });
                if (!isValid) {
                    alert('Please fill in all required fields for the new asset.');
                    return;
                }
                let collapseInstance = bootstrap.Collapse.getInstance(newAssetForm);
                if (!collapseInstance) {
                    collapseInstance = new bootstrap.Collapse(newAssetForm);
                }
                collapseInstance.hide();

                // Add a "dummy" option to the asset select that shows the new asset name
                const newAssetName = item.querySelector('.new-asset-name').value;
                const newOption = document.createElement('option');
                newOption.value = 'new_asset';
                newOption.textContent = `${newAssetName} (New)`;
                newOption.selected = true;
                assetSelect.appendChild(newOption);
            });
        }

        // Function to toggle buyer selection based on type (debit/credit)
        function setupBuyerSelectionToggle(item) {
            const typeSelect = item.querySelector('.type-select');
            const buyerSelectionContainer = item.querySelector('.buyer-selection-container');
            const buyerSelect = item.querySelector('.buyer-select');
            // Initial toggle based on default value
            if (typeSelect.value === 'debit') {
                buyerSelectionContainer.style.display = 'block';
                buyerSelect.setAttribute('required', 'required');
            } else {
                buyerSelectionContainer.style.display = 'none';
                buyerSelect.removeAttribute('required');
            }
            typeSelect.addEventListener('change', function() {
                if (this.value === 'debit') {
                    buyerSelectionContainer.style.display = 'block';
                    buyerSelect.setAttribute('required', 'required');
                } else {
                    buyerSelectionContainer.style.display = 'none';
                    buyerSelect.removeAttribute('required');
                }
            });
        }

        // Add new item
        addItemBtn.addEventListener('click', function() {
            const newItem = document.importNode(itemTemplate.content, true).firstElementChild;

            // Remove item functionality
            const removeBtn = newItem.querySelector('.remove-item-btn');
            removeBtn.addEventListener('click', function() {
                newItem.remove();
                updateItemNumbers();
            });

            setupNewAssetForm(newItem);
            setupBuyerSelectionToggle(newItem);

            itemsContainer.appendChild(newItem);
            itemCount++;
            updateItemNumbers();
        });

        // Initialize form
        updateItemNumbers();
    });
</script>

@endsection
