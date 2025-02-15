<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Account;
use App\Models\Currency;
use App\Models\Supplier;
use App\Models\AssetType;
use App\Models\AssetStatus;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\AssetCategory;
use App\Models\TransactionInfo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user(); // Retrieve the user object
        if (!$user) {
            return redirect('/login'); // Redirect to login if not authenticated
        }

        // Start building the query for the user's transactions
        $query = Transaction::where('user_id', $user->id);

        // If an account type filter is provided, filter transactions by account type.
        if ($request->filled('acc_type')) {
            $accType = $request->input('acc_type');
            $query->whereHas('account', function ($q) use ($accType) {
                $q->where('acc_type', $accType);
            });
        }

        // If a transaction type filter (credit/debit) is provided, filter the transactions.
        if ($request->filled('trans_type')) {
            $transType = $request->input('trans_type');
            $query->where('type', $transType);
        }

        // If a search query is provided, filter transactions by description.
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('description', 'like', '%' . $search . '%');
        }

        // Paginate the filtered transactions
        $transs = $query->paginate(9);

        return view('transactions.transactions', compact('transs'));
    }

    public function show()
    {
        $user = Auth::user();
        $assets = Asset::where('user_id', $user->id)->get();
        $currencies = Currency::where('user_id', $user->id)->get();
        $accounts = Account::where('user_id', $user->id)->get();
        $assetStatuses = AssetStatus::all();
        $assetCategories = AssetCategory::where('user_id', $user->id)->get();
        $assetTypes = AssetType::where('user_id', $user->id)->get();
        $buyers = Supplier::where('user_id', $user->id)->get();
        // liabilities
        // equities
        // expenses
        // revenues
        return view('transactions.transaction_create', compact('assets', 'currencies', 'accounts', 'assetTypes', 'assetStatuses', 'assetCategories', 'buyers'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'currency_id' => 'required|exists:currencies,id',
            'buyer_id' => 'required|exists:buyers,id',
            'account_id' => 'required|exists:accounts,id',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:credit,debit',
            'total' => 'required|numeric|min:0.01',
            'items' => 'required|array|min:1',
            'items.*.amount' => 'required|numeric|min:0.01',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.purchase_price' => 'required|numeric|min:0',
            'items.*.current_price' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string|max:1000',

            // Asset validation - either existing or new
            'items.*.asset_id' => 'required_without:items.*.new_asset|exists:assets,id|nullable',

            // New asset validation
            'items.*.new_asset.reference_number' => 'required_with:items.*.new_asset|string|max:255|nullable',
            'items.*.new_asset.name' => 'required_with:items.*.new_asset|string|max:255|nullable',
            'items.*.new_asset.asset_type_id' => 'required_with:items.*.new_asset|exists:asset_types,id|nullable',
            'items.*.new_asset.asset_category_id' => 'required_with:items.*.new_asset|exists:asset_categories,id|nullable',
            'items.*.new_asset.asset_status_id' => 'required_with:items.*.new_asset|exists:asset_statuses,id|nullable',
            'items.*.new_asset.location' => 'nullable|string|max:255',
            'items.*.new_asset.current_value' => 'required_with:items.*.new_asset|numeric|min:0|nullable',
            'items.*.new_asset.purchase_price' => 'nullable|numeric|min:0',
            'items.*.new_asset.purchase_at' => 'nullable|date',
            'items.*.new_asset.quantity' => 'nullable|integer|min:1',
            'items.*.new_asset.notes' => 'nullable|string|max:1000',
        ]);

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Create the main transaction record
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'account_id' => $validated['account_id'],
                'buyer_id' => $validated['buyer_id'],
                'status' => 'pending', // Default status
                'type' => $validated['type'],
                'total' => $validated['total'],
                'description' => $validated['description'] ?? null,
                'transaction_date' => $validated['transaction_date'],
            ]);

            // Process each transaction item
            foreach ($validated['items'] as $itemData) {
                // Handle asset creation if needed
                if (isset($itemData['new_asset']) && !isset($itemData['asset_id'])) {
                    // Create new asset
                    $asset = Asset::create([
                        'reference_number' => $itemData['new_asset']['reference_number'],
                        'user_id' => $user->id,
                        'name' => $itemData['new_asset']['name'],
                        'asset_type_id' => $itemData['new_asset']['asset_type_id'],
                        'asset_category_id' => $itemData['new_asset']['asset_category_id'],
                        'asset_status_id' => $itemData['new_asset']['asset_status_id'],
                        'location' => $itemData['new_asset']['location'] ?? null,
                        'current_value' => $itemData['new_asset']['current_value'],
                        'purchase_price' => $itemData['new_asset']['purchase_price'] ?? null,
                        'purchase_at' => $itemData['new_asset']['purchase_at'] ?? null,
                        'quantity' => $itemData['new_asset']['quantity'] ?? 1,
                        'notes' => $itemData['new_asset']['notes'] ?? null,
                        'currency_id' => $validated['currency_id'],
                    ]);

                    $assetId = $asset->id;
                } else {
                    // Use existing asset
                    $assetId = $itemData['asset_id'];
                }

                // Create transaction info
                TransactionInfo::create([
                    'transaction_id' => $transaction->id,
                    'transactionable_type' => Asset::class,
                    'transactionable_id' => $assetId,
                    'quantity' => $itemData['quantity'],
                    'amount' => $itemData['amount'],
                    'purchase_price' => $itemData['purchase_price'],
                    'current_price' => $itemData['current_price'],
                ]);
            }

            DB::commit();

            return redirect()
                ->route('transactions.show', $transaction)
                ->with('success', 'Transaction created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => 'Error creating transaction: ' . $e->getMessage()]);
        }
    }
}
