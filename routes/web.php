<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\EquityController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AssetCategoryController;
use App\Http\Controllers\AssetStatusController;
use App\Http\Controllers\AssetTypeController;
use App\Http\Controllers\currencyController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\LiabilityController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\RegisteredUserController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/register', [RegisteredUserController::class, 'create']);
Route::POST('/register', [RegisteredUserController::class, 'store']);

Route::get('/login', [SessionController::class, 'create']);
Route::POST('/login', [SessionController::class, 'store'])->name('login');
Route::POST('/logout', [SessionController::class, 'destroy'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/create', [AccountController::class, 'create'])->name('create.acc.show');
    Route::post('/create', [AccountController::class, 'store'])->name('create-acc');
    Route::delete('/accounts/{account}', [AccountController::class, 'destroy'])->name('accounts.destroy');
    Route::post('/accounts/{account}/add-balance', [AccountController::class, 'addBalance'])->name('accounts.add-balance');
});

Route::get('/transactions', [TransactionController::class, 'index'])->middleware('auth')->name('transactions.show');
Route::POST('/transactions/create', [TransactionController::class, 'createNewTransaction'])->middleware('auth')->name('create_transaction.create');
Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->middleware('auth')->name('transaction_create.show');
Route::POST('/transaction/{transaction}', [TransactionController::class, 'buyAsset'])->middleware('auth')->name("transaction_asset.store");
Route::PATCH('/transaction/{transaction}', [TransactionController::class, 'sellAsset'])->middleware('auth')->name("transaction_asset.sell");



Route::get('/asset_type', [AssetTypeController::class, 'show'])->name('asset_types.show')->middleware('auth');
Route::POST('/asset_type/store', [AssetTypeController::class, 'store'])->name('asset_types.store')->middleware('auth');
Route::delete('/asset_type/{assetType}', [AssetTypeController::class, 'destroy'])->name('asset_types.destroy')->middleware('auth');
Route::put('/asset_types/{assetType}', [AssetTypeController::class, 'update'])->name('asset_types.update')->middleware('auth');

Route::get('/asset_status', [AssetStatusController::class, 'show'])->name('asset_statuses.show')->middleware('auth');
Route::POST('/asset_status/store', [AssetStatusController::class, 'store'])->name('asset_statuses.store')->middleware('auth');
Route::delete('/asset_status/{assetStatus}', [AssetStatusController::class, 'destroy'])->name('asset_statuses.destroy')->middleware('auth');
Route::put('/asset_status/{assetStatus}', [AssetStatusController::class, 'update'])->name('asset_statuses.update')->middleware('auth');

Route::get('/asset_categories', [AssetCategoryController::class, 'show'])->name('asset_categories.show')->middleware('auth');
Route::POST('/asset_categories', [AssetCategoryController::class, 'store'])->name('asset_categories.store')->middleware('auth');
Route::put('/asset_categories/{assetCategory}', [AssetCategoryController::class, 'update'])->name('asset_categories.update')->middleware('auth');
Route::delete('/asset_categories/{assetCategory}', [AssetCategoryController::class, 'destroy'])->name('asset_categories.destroy')->middleware('auth');

Route::get('/assets', [AssetController::class, 'show'])->name('assets.show')->middleware('auth');
Route::get('/assets/create', [AssetController::class, 'create'])->name('assets.create')->middleware('auth');
Route::post('/assets', [AssetController::class, 'store'])->name('assets.store')->middleware('auth');
Route::get('/assets/{id}', [AssetController::class, 'detail'])->name('assets_detials.show')->middleware('auth');
Route::post('/assets/sell/{id}', [AssetController::class, 'sell'])->name('assets.sell')->middleware('auth');
Route::get('/asset/update/{assetType}', [AssetTypeController::class, 'showUpdate'])->name('asset_update.show')->middleware('auth');

Route::get('/liabilities', [LiabilityController::class, 'index'])->name('liabilities.index')->middleware('auth');
Route::get('/liabilities/create', [LiabilityController::class, 'create'])->name('liabilities.create')->middleware('auth');
Route::POST('/liabilities/create', [LiabilityController::class, 'store'])->name('liabilities.store')->middleware('auth');
Route::get('/liabilities/{liability}/edit', [LiabilityController::class, 'pay'])->name('liabilities.pay');
Route::put('/liabilities/{liability}/pay', [LiabilityController::class, 'payUpdate'])->name('liabilities.pay.update');
Route::get('/liabilities/{id}', [LiabilityController::class, 'show'])->name('liabilities.show')->middleware('auth');
Route::get('/liabilities/{liability}', [LiabilityController::class, 'show'])->name('liabilities.show')->middleware('auth');

Route::get('/equities', [EquityController::class, 'index'])->name('equities.index')->middleware('auth');
Route::get('/equities/create', [EquityController::class, 'create'])->name('equities.create')->middleware('auth');
Route::POST('/equities/create', [EquityController::class, 'store'])->name('equities.store')->middleware('auth');
Route::get('/equities/{equity}', [EquityController::class, 'show'])->name('equities.show')->middleware('auth');
Route::post('/equities/{equity}/sell', [EquityController::class, 'sell'])->name('equities.sell');

Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index')->middleware('auth');
Route::get('/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create')->middleware('auth');
Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store')->middleware('auth');
Route::get('/expenses/{expense}', [ExpenseController::class, 'show'])->name('expenses.show')->middleware('auth');
Route::put('expenses/{expense}/pay', [ExpenseController::class, 'pay'])->name('expenses.pay.update');

Route::get('/currencies', [CurrencyController::class, 'index'])->name('currencies.currencies')->middleware('auth');
Route::post('/currencies', [CurrencyController::class, 'store'])->name('currencies.store')->middleware('auth');
Route::delete('/currencies/{currency}', [CurrencyController::class, 'destroy'])->name('currencies.destroy');
Route::put('/currencies/{currency}', [CurrencyController::class, 'update'])->name('currencies.update');

