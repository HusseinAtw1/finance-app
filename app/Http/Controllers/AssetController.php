<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Account;
use App\Models\Currency;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
{
    public function show(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Get all asset accounts for the user
        $accounts = Account::where('user_id', $user->id)
                    ->where('acc_type', 'asset')
                    ->get();

        // Selected account from the query string (or default to the first account if available)
        $selectedAccount = $request->input('account_id', $accounts->first()->id ?? null);

        // Get the status filter: "owned" or "sold" (default to owned)
        $status = $request->input('status', 'owned');

        // Get the search term if provided
        $search = $request->input('search');

        // Build the assets query
        $assets = Asset::where('user_id', $user->id)
            ->when($selectedAccount, function ($query, $selectedAccount) {
                return $query->where('account_id', $selectedAccount);
            })
            ->when($status, function ($query, $status) {
                if ($status === 'sold') {
                    return $query->where('owns', false);
                } elseif ($status === 'owned') {
                    return $query->where('owns', true);
                }
                return $query;
            })
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('notes', 'like', "%{$search}%");
                });
            })
            ->paginate(9);

        // Pass the additional variables to the view as well
        return view('assets.assets', compact('assets', 'accounts', 'selectedAccount', 'status', 'search'));
    }

    public function create()
    {
        $user = Auth::user();
        $accs = Account::where('user_id', $user->id)
                    ->where('acc_type', 'asset')
                    ->get();
        $currencies = Currency::all();

        return view('assets.create', compact('accs', 'currencies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'type'           => 'required|string|in:cash,investment,property',
            'current_value'  => 'required|numeric|min:0',
            'purchase_date'  => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'category'       => 'nullable|in:fixed,liquid',
            'location'       => 'nullable|string|max:255',
            'notes'          => 'nullable|string',
            'account_id'     => 'required|exists:accounts,id',
        ]);

        $user = Auth::user();

        // Create the asset
        $asset = Asset::create([
            'user_id'        => $user->id,
            'account_id'     => $request->account_id,
            'name'           => $request->name,
            'type'           => $request->type,
            'current_value'  => $request->current_value,
            'purchase_date'  => $request->purchase_date,
            'purchase_price' => $request->purchase_price,
            'category'       => $request->category,
            'location'       => $request->location,
            'notes'          => $request->notes,
        ]);

        // Determine the transaction amount:
        // Use purchase price if provided, otherwise current value.
        $transactionAmount = $request->purchase_price ?? $request->current_value;

        // Create a corresponding transaction using polymorphic relationships
        Transaction::create([
            'user_id'              => $user->id,
            'account_id'           => $request->account_id,
            'transactionable_id'   => $asset->id,
            'transactionable_type' => Asset::class,
            'amount'               => $transactionAmount,
            'type'                 => 'debit', // Asset purchase is recorded as a debit transaction
            'description'          => "Purchased asset: {$request->name}",
            'transaction_date'     => $request->purchase_date,
        ]);

        // Update the account balance by deducting the transaction amount.
        // (Assumes purchasing an asset decreases your cash balance.)
        $account = Account::find($request->account_id);
        if ($account) {
            $account->balance -= $transactionAmount;
            $account->save();
        }

        return redirect()->route('assets.show')->with('success', 'Asset added successfully and transaction recorded!');
    }

    public function detail($id)
    {
        $asset = Asset::findOrFail($id); // Fetch the asset by ID
        return view('assets.detail', compact('asset')); // Pass data to the view
    }

    public function sell(Request $request, $id)
    {
        // Validate incoming data. Ensure account_id is provided if it's required.
        $validated = $request->validate([
            'sell_price' => ['required', 'numeric', 'min:0.01'],
            'sold_at'    => ['required', 'date'],
            'account_id' => ['required', 'exists:accounts,id'], // if account_id is needed for transactions
        ]);

        // Retrieve the asset
        $asset = Asset::findOrFail($id);

        // Update the asset to mark it as sold.
        // Notice: We mark the asset as no longer owned.
        $asset->update([
            'owns'       => false,
            'sell_price' => $validated['sell_price'],
            'sell_date'  => $validated['sold_at']
        ]);

        // Get the authenticated user.
        $user = Auth::user();

        // The transaction amount will be the sell price.
        $transactionAmount = $validated['sell_price'];

        // Create a new transaction record.
        Transaction::create([
            'user_id'              => $user->id,
            'account_id'           => $validated['account_id'],
            'transactionable_id'   => $asset->id,
            'transactionable_type' => Asset::class,
            'amount'               => $transactionAmount,
            'type'                 => 'credit', // For a sale, it’s a credit transaction.
            'description'          => "Sold asset: {$asset->name}",
            'transaction_date'     => $validated['sold_at'],
        ]);

        // Update the account balance by adding the sell price.
        // (Selling an asset increases your cash balance.)
        $account = Account::find($validated['account_id']);
        if ($account) {
            $account->balance += $transactionAmount;
            $account->save();
        }

        // Redirect back with a success message.
        return redirect()->route('assets.show', $id)
                         ->with('success', 'Asset sold successfully!');
    }
}
