<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Account;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\AssetType;
use App\Models\AssetStatus;
use App\Models\Transaction;
use App\Models\Depreciation;
use Illuminate\Http\Request;
use App\Models\AssetCategory;
use App\Models\TransactionInfo;
use Illuminate\Validation\Rule;
use App\Models\AssetDepreciation;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $transactions = Transaction::where('user_id', $user->id)->paginate(5);
        return view('transactions.transactions', compact('transactions'));
    }

    public function createNewTransaction()
    {
        $user = Auth::user();
        $transaction = Transaction::create([
            'user_id'    => $user->id,
            'status'     => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $currencies =  Currency::where('user_id', $user->id)->get();
        $depreciations = AssetDepreciation::all();
        $assets = Asset::where('user_id', $user->id)->get();
        $suppliers = Supplier::where('user_id', $user->id)->get();
        $accounts = Account::where('user_id', $user->id)->get();
        $assetCategories = AssetCategory::where('user_id', $user->id)->get();
        $assetTypes = AssetType::where('user_id', $user->id)->get();
        $transactionDetails = TransactionDetail::where('transaction_id', $transaction->id)->get();
        $assets = Asset::where('user_id', $user->id)->get();
        $currencies =  Currency::where('user_id', $user->id)->get();
        return view('transactions.transaction_create', compact('transaction', 'transactionDetails', 'assets', 'currencies', 'depreciations', 'suppliers', 'accounts', 'assetTypes', 'assetCategories'));
    }

    public function show(Transaction $transaction)
    {
        $user = Auth::user();
        $currencies =  Currency::where('user_id', $user->id)->get();
        $depreciations = AssetDepreciation::all();
        $assets = Asset::where('user_id', $user->id)->get();
        $suppliers = Supplier::where('user_id', $user->id)->get();
        $accounts = Account::where('user_id', $user->id)->get();
        $assetCategories = AssetCategory::where('user_id', $user->id)->get();
        $assetTypes = AssetType::where('user_id', $user->id)->get();
        $transactionDetails = TransactionDetail::where('transaction_id', $transaction->id)->get();
        return view('transactions.transaction_create', compact('transaction', 'transactionDetails', 'assets', 'currencies', 'depreciations', 'suppliers', 'accounts', 'assetTypes', 'assetCategories'));
    }

    public function buyAsset(Request $request, Transaction $transaction)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'              => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request, $user) {
                    $exists = Asset::where('user_id', $user->id)
                        ->where('name', $value)
                        ->where('reference_number', $request->reference_number)
                        ->exists();
                    if ($exists) {
                        $fail('An asset with this name and reference number already exists.');
                    }
                }],
            'reference_number'  => 'required|string|max:100',
            'asset_category'    => 'required|exists:asset_categories,id',
            'asset_type'        => 'required|exists:asset_types,id',
            'currency_id'       => 'required|exists:currencies,id',
            'depreciation_id'   => 'nullable|exists:asset_depreciations,id',
            'current_value'     => 'required|numeric|min:0',
            'purchase_price'    => 'required|numeric|min:0',
            'purchase_date'     => 'required|date',
            'location'          => 'required|string|max:255',
            'supplier_id'       => 'required|exists:suppliers,id',
            'notes'             => 'nullable|string',
            'account_id'        => 'required|exists:accounts,id',
            'quantity'          => 'required|integer|min:1',
        ]);

        $status = AssetStatus::where('name', 'Pending')->first();

        $asset = Asset::create([
            'user_id'               => $user->id,
            'reference_number'      => $validated['reference_number'],
            'currency_id'           => $validated['currency_id'],
            // 'currency_exchange_rate'=> $request->currency_exchange_rate ?? null,
            'asset_type_id'         => $validated['asset_type'],
            'asset_category_id'     => $validated['asset_category'],
            'asset_status_id'       => $status->id,
            'asset_depreciation_id' => $validated['depreciation_id'],
            'name'                  => $validated['name'],
            'quantity'              => $validated['quantity'],
            'current_value'         => $validated['current_value'],
            'purchase_price'        => $validated['purchase_price'],
            'location'              => $validated['location'] ?? null,
            'notes'                 => $validated['notes'] ?? null,
            'purchase_at'           => $validated['purchase_date'],
            'created_at'            => now(),
            'updated_at'            => now(),
        ]);

        $transactionDetail = TransactionDetail::create([
            'transaction_id'        => $transaction->id,
            'transactionable_type'  => Asset::class,
            'transactionable_id'    => $asset->id,
            'account_id'            => $request->account_id,
            'supplier_id'           => $request->supplier_id,
            'customer_id'           => null,
            'type'                  => 'debit',
            'current_price'         => $request->current_value,
            'purchase_price'        => $request->purchase_price,
            'sold_for'              => null,
            'quantity'              => $request->quantity,
            'amount'                => $request->purchase_price * $request->quantity,
            'created_at'            => now(),
            'updated_at'            => now(),
        ]);

        return back();

    }


    public function sellAsset(Request $request, Transaction $transaction)
    {
        return 'this is sell form';
	}





}
