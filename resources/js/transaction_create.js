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
const locationDiv         = document.getElementById("locationDiv");
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
const locationInput       = document.getElementById('location');
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
    setVisibility(locationDiv,         locationInput,       'block', true);
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
    setVisibility(locationDiv,         locationInput,       'none', false);
    setVisibility(supplierDiv,         supplierInput,       'none', false);
    setVisibility(notesDiv,            notesInput,          'none', false);

    // Show sell-specific fields and mark them as required
    setVisibility(customerNameDiv,     customerInput,       'block', true);
    setVisibility(customerNumberDiv,   customerNumberInput, 'block', true);
    setVisibility(soldForDiv,          soldForInput,        'block', true);
    setVisibility(assetDiv,            assetInput,          'block', true);
    setVisibility(sellDateDiv,         soldDateinput,       'block', true);
};

window.document.addEventListener('DOMContentLoaded', function() {
    buyAssetForm();
});
