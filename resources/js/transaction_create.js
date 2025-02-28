import axios from 'axios';

const actionField         = document.getElementById('action');
const nameDiv             = document.getElementById('nameDiv');
const referenceNumberDiv  = document.getElementById('referenceNumberDiv');
const assetCategoryDiv    = document.getElementById('assetCategoryDiv');
const assetTypeDiv        = document.getElementById('assetTypeDiv');
const accountDiv          = document.getElementById('accountDiv');
const currencyDiv         = document.getElementById('currencyDiv');
const depreciationDiv     = document.getElementById('depreciationDiv');
const transactionTypeDiv  = document.getElementById('transactionTypeDiv');
const quantityDiv         = document.getElementById('quantityDiv');
const currentValueDiv     = document.getElementById('currentValueDiv');
const purchasePriceDiv    = document.getElementById('purchasePriceDiv');
const purchaseDateDiv     = document.getElementById('purchaseDateDiv');
const storageDiv          = document.getElementById("storageDiv");
const supplierDiv         = document.getElementById('supplierDiv');
const customerNameDiv     = document.getElementById('customerNameDiv');
const customerNumberDiv   = document.getElementById('customerNumberDiv');
const soldForDiv          = document.getElementById('soldForDiv');
const notesDiv            = document.getElementById('notesDiv');
const assetDiv            = document.getElementById('assetDiv');
const sellDateDiv         = document.getElementById('sellDateDiv');

const soldDateinput       = document.getElementById('sold_at');
const assetInput          = document.getElementById('asset_id')
const nameInput           = document.getElementById('name');
const referenceNumberInput= document.getElementById('reference_number');
const assetCategoryInput  = document.getElementById('asset_category');
const assetTypeInput      = document.getElementById('asset_type');
const accountInput        = document.getElementById('account_id');
const currencyInput       = document.getElementById('currency');
const depreciationInput   = document.getElementById('depreciation');
const transactionTypeInput= document.getElementById('transaction_type');
const quantityInput       = document.getElementById('quantity');
const currentValueInput   = document.getElementById('current_value');
const purchasePriceInput  = document.getElementById('purchase_price');
const purchaseDateInput   = document.getElementById('purchase_date');
const storage_id          = document.getElementById('storage_id');
const supplierInput       = document.getElementById('supplier_id');
const customerInput       = document.getElementById('customer');
const customerNumberInput = document.getElementById('customer_number');
const soldForInput        = document.getElementById('sold_for');
const notesInput          = document.getElementById('notes');

const formField           = document.getElementById('assetForm');

const transactionTypeOption = document.getElementById('transaction_type_option');

// Function to update both visibility and required status
function setVisibility(div, input, displayValue, isRequired) {
    if(div) { div.style.display = displayValue; }
    if(input) { input.required = isRequired; }
}

window.buyAssetForm = function buyAssetForm() {
    actionField.innerHTML = 'Buy';
    document.getElementById('methodInput').value = 'POST';
    formField.action = window.storeAssetUrl;
    transactionTypeOption.value = 'debit'
    transactionTypeOption.innerHTML = 'Debit'
    // Show buy-specific fields and mark them as required
    setVisibility(nameDiv,             nameInput,           'block', true);
    setVisibility(referenceNumberDiv,  referenceNumberInput,'block', true);
    setVisibility(assetCategoryDiv,    assetCategoryInput,  'block', true);
    setVisibility(assetTypeDiv,        assetTypeInput,      'block', true);
    setVisibility(currencyDiv,         currencyInput,       'block', true);
    setVisibility(depreciationDiv,     depreciationInput,   'block', true);
    setVisibility(currentValueDiv,     currentValueInput,   'block', true);
    setVisibility(purchasePriceDiv,    purchasePriceInput,  'block', true);
    setVisibility(purchaseDateDiv,     purchaseDateInput,   'block', true);
    setVisibility(storageDiv,          storage_id,          'block', true);
    setVisibility(supplierDiv,         supplierInput,       'block', true);
    setVisibility(notesDiv,            notesInput,          'block', true);

    // Show common fields
    setVisibility(accountDiv,          accountInput,        'block', true);
    setVisibility(transactionTypeDiv,  transactionTypeInput,'block', true);
    setVisibility(quantityDiv,         quantityInput,       'block', true);

    // Hide sell-specific fields and remove their required attribute
    setVisibility(customerNameDiv,     customerInput,       'none', false);
    setVisibility(customerNumberDiv,   customerNumberInput, 'none', false);
    setVisibility(soldForDiv,          soldForInput,        'none', false);
    setVisibility(assetDiv,            assetInput,          'none', false);
    setVisibility(sellDateDiv,         soldDateinput,       'none', false);
};

window.sellAssetForm = function sellAssetForm() {
    actionField.innerHTML = 'Sell';
    document.getElementById('methodInput').value = 'PATCH';
    formField.action = window.sellAssetUrl;
    transactionTypeOption.value = 'credit'
    transactionTypeOption.innerHTML = 'Credit'
    // Show common fields
    setVisibility(accountDiv,          accountInput,        'block', true);
    setVisibility(transactionTypeDiv,  transactionTypeInput,'block', true);
    setVisibility(quantityDiv,         quantityInput,       'block', true);

    // Hide buy-specific fields and remove their required attribute
    setVisibility(nameDiv,             nameInput,           'none', false);
    setVisibility(referenceNumberDiv,  referenceNumberInput,'none', false);
    setVisibility(assetCategoryDiv,    assetCategoryInput,  'none', false);
    setVisibility(assetTypeDiv,        assetTypeInput,      'none', false);
    setVisibility(currencyDiv,         currencyInput,       'none', false);
    setVisibility(depreciationDiv,     depreciationInput,   'none', false);
    setVisibility(currentValueDiv,     currentValueInput,   'none', false);
    setVisibility(purchasePriceDiv,    purchasePriceInput,  'none', false);
    setVisibility(purchaseDateDiv,     purchaseDateInput,   'none', false);
    setVisibility(storageDiv,          storage_id,          'none', false);
    setVisibility(supplierDiv,         supplierInput,       'none', false);
    setVisibility(notesDiv,            notesInput,          'none', false);

    // Show sell-specific fields and mark them as required
    setVisibility(customerNameDiv,     customerInput,       'block', true);
    setVisibility(customerNumberDiv,   customerNumberInput, 'block', true);
    setVisibility(soldForDiv,          soldForInput,        'block', true);
    setVisibility(assetDiv,            assetInput,          'block', true);
    setVisibility(sellDateDiv,         soldDateinput,       'block', true);
};

function displayErrors(errors) {
    // Clear any existing error messages first
    document.querySelectorAll('.text-danger').forEach(element => {
        if (!element.classList.contains('d-none')) {
            element.innerHTML = '';
        }
    });

    // Display new error messages
    for (const field in errors) {
        const errorDiv = document.querySelector(`[name="${field}"]`).nextElementSibling;
        if (errorDiv && errorDiv.classList.contains('text-danger')) {
            errorDiv.innerHTML = errors[field][0]; // Display the first error message
        } else {
            // Create error element if it doesn't exist
            const input = document.querySelector(`[name="${field}"]`);
            if (input) {
                const div = document.createElement('div');
                div.className = 'text-danger';
                div.innerHTML = errors[field][0];
                input.parentNode.appendChild(div);
            }
        }
    }
}

// Function to show success message
function showNotification(message, type = 'success') {
    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.role = 'alert';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

    // Insert at the top of the container
    const container = document.querySelector('.container.mt-5');
    container.insertBefore(alertDiv, container.firstChild);

    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        alertDiv.classList.remove('show');
        setTimeout(() => alertDiv.remove(), 150);
    }, 5000);
}

function refreshTransactionDetails() {
    axios.get(window.location.href)
        .then(response => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(response.data, 'text/html');
            const newTable = doc.querySelector('.table-responsive');
            const currentTable = document.querySelector('.table-responsive');

            if (newTable && currentTable) {
                currentTable.innerHTML = newTable.innerHTML;
            }
        })
        .catch(error => {
            console.error('Error refreshing table:', error);
        });
}

function submitBuyAssetForm(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    // Add CSRF token
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Submit using Axios
    axios.post(window.storeAssetUrl, formData, {
        headers: {
            'X-CSRF-TOKEN': token,
            'Content-Type': 'multipart/form-data'
        }
    })
    .then(response => {
        showNotification('Asset successfully added to transaction!');
        refreshTransactionDetails();
        form.reset();
        buyAssetForm(); // Reset form to buy state
    })
    .catch(error => {
        if (error.response && error.response.status === 422) {
            // Validation errors
            displayErrors(error.response.data.errors);
            showNotification('Please fix the errors in the form.', 'danger');
        } else {
            // Server error or other issues
            showNotification('An error occurred while processing your request.', 'danger');
            console.error('Error:', error);
        }
    });
}

function submitSellAssetForm(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    // Add CSRF token and method override for Laravel
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Submit using Axios
    axios.post(window.sellAssetUrl, formData, {
        headers: {
            'X-CSRF-TOKEN': token,
            'Content-Type': 'multipart/form-data',
            'X-HTTP-Method-Override': 'PATCH'
        }
    })
    .then(response => {
        showNotification('Asset successfully sold!');
        refreshTransactionDetails();
        form.reset();
        sellAssetForm(); // Reset form to sell state
    })
    .catch(error => {
        if (error.response && error.response.status === 422) {
            // Validation errors
            displayErrors(error.response.data.errors);
            showNotification('Please fix the errors in the form.', 'danger');
        } else {
            // Server error or other issues
            showNotification('An error occurred while processing your request.', 'danger');
            console.error('Error:', error);
        }
    });
}

window.document.addEventListener('DOMContentLoaded', function() {
    buyAssetForm(); // Set default form state

    // Attach form submit handler
    formField.addEventListener('submit', function(e) {
        // Determine which form type is active
        if (actionField.innerHTML === 'Buy') {
            submitBuyAssetForm(e);
        } else if (actionField.innerHTML === 'Sell') {
            submitSellAssetForm(e);
        }
    });
});


const assetTemplate = document.getElementById('assetTemplate');
assetTemplate.style.display = 'none';

window.showAssetForm = function() {
    assetTemplate.style.display = 'block';
}

window.showLiabilityForm = function() {
    assetTemplate.style.display = 'none';
}

window.showExpenseForm = function() {
    assetTemplate.style.display = 'none';
}

window.hideAllForms = function() {
    assetTemplate.style.display = 'none';
}

window.deleteTransactionDetail = function(id) {
    // Ask for confirmation before deleting
    if (!confirm("Are you sure you want to delete this transaction detail?")) {
        return;
    }

    // Retrieve CSRF token from the meta tag
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Construct the URL by appending the id to the base URL
    const url = window.deleteTransactionDetailUrl + '/' + id;

    axios.delete(url, {
        headers: {
            'X-CSRF-TOKEN': token
        }
    })
    .then(response => {
        if (response.data.success) {
            showNotification(response.data.message);
            refreshTransactionDetails();
        } else {
            showNotification('Failed to delete transaction detail.', 'danger');
        }
    })
    .catch(error => {
        showNotification('An error occurred while deleting the transaction detail.', 'danger');
        console.error('Delete error:', error);
    });
};




