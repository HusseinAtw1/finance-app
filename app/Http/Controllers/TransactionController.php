<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Account;
use App\Models\Storage;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\AssetType;
use App\Models\AssetStatus;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\AssetCategory;
use Illuminate\Validation\Rule;
use App\Models\AssetDepreciation;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $transactions = Transaction::where('user_id', $user->id)->paginate(5);
        return view('transactions.transactions', compact('transactions'));
    }

    public function show(Transaction $transaction)
    {
        $user = Auth::user();

        $currencies =  Currency::where('user_id', $user->id)->get();

        $depreciations = AssetDepreciation::all();

        $soldStatus = AssetStatus::where('name', 'Sold')->first();

        $pendingStatus = AssetStatus::where('name', 'Pending')->first();

        $assets = Asset::where('user_id', $user->id)
            ->where('asset_status_id', '!=', $soldStatus->id)
            ->where('asset_status_id', '!=', $pendingStatus->id)
            ->get();

        $suppliers = Supplier::where('user_id', $user->id)->get();

        $accounts = Account::where('user_id', $user->id)->get();

        $assetCategories = AssetCategory::where('user_id', $user->id)->get();

        $assetTypes = AssetType::where('user_id', $user->id)->get();

        $transactionDetails = TransactionDetail::with(['account', 'supplier', 'customer'])->where('transaction_id', $transaction->id)->get();

        $storages = Storage::where('user_id', $user->id)->get();

            return view('transactions.transaction_create', compact('transaction', 'transactionDetails', 'assets', 'currencies', 'depreciations', 'suppliers', 'accounts', 'assetTypes', 'assetCategories', 'storages'));
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

        return $this->show($transaction);
    }

}
