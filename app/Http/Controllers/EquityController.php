<?php

namespace App\Http\Controllers;

use App\Models\Equity;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class EquityController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Retrieve all accounts for the authenticated user.
        $accounts = Account::where('user_id', $user->id)->where('acc_type', 'equity')->get();

        // Determine the selected account.
        // If none is provided and there is at least one account, default to the first account's id.
        $selectedAccount = $request->input('account_id', $accounts->first()->id ?? null);

        // Retrieve the filter status, defaulting to 'all' if not provided.
        $filterStatus = $request->input('filter_status', 'all');

        // Build the base query for equities belonging to the user.
        $query = Equity::query()->where('user_id', $user->id);

        // Adjust the query based on the filter status.
        if ($filterStatus === 'inactive') {
            // Only include soft-deleted records (inactive equities).
            $query->onlyTrashed();
        } elseif ($filterStatus === 'all') {
            // Include both active and inactive equities.
            $query->withTrashed();
        }
        // For 'active', no change is needed because soft-deleted records are excluded by default.

        // Filter by selected account if available.
        if ($selectedAccount) {
            $query->where('account_id', $selectedAccount);
        }

        // Apply search filter to search by name or symbol.
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('symbol', 'like', '%' . $search . '%');
            });
        }

        // Paginate the results (9 per page, adjust as needed).
        $equities = $query->paginate(9);

        // Return the view with the required variables.
        return view('equities.index', compact('equities', 'accounts', 'selectedAccount'));
    }


    public function create()
    {
        // Get the authenticated user.
        $user = Auth::user();

        // Retrieve accounts belonging to the user.
        $accounts = Account::where('user_id', $user->id)
        ->where('acc_type', 'equity')
        ->get();

        // Pass the accounts to the view.
        return view('equities.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        // Validate the request data.
        $validatedData = $request->validate([
            'account_id'         => 'required|exists:accounts,id',
            'name'               => 'required|string|max:255',
            'symbol'             => 'required|string|max:10',
            'purchase_price'     => 'required|numeric',
            'current_price'      => 'nullable|numeric',
            'quantity'           => 'required|integer',
            'currency'           => 'required|string|size:3',
            'sector'             => 'nullable|string|max:255',
            'dividends_received' => 'nullable|numeric',
            'purchased_at'       => 'nullable|date',
            'sold_at'            => 'nullable|date',
        ]);

        // If a purchase date is provided, convert it using Carbon.
        if (!empty($validatedData['purchased_at'])) {
            $validatedData['purchased_at'] = Carbon::parse($validatedData['purchased_at']);
        }

        // Calculate the total amount for the equity.
        $validatedData['amount'] = $validatedData['purchase_price'] * $validatedData['quantity'];

        // Set additional fields.
        $validatedData['transaction_type'] = 'buy';
        $validatedData['user_id'] = Auth::id();

        // Create the equity record.
        $equity = Equity::create($validatedData);

        // Create the associated transaction record.
        Transaction::create([
            'user_id'              => Auth::id(),
            'account_id'           => $validatedData['account_id'],
            'transactionable_id'   => $equity->id,
            'transactionable_type' => Equity::class,
            // Mark "buy" transactions as debits.
            'type'                 => 'debit',
            'amount'               => $validatedData['amount'],
            'description'          => 'Equity purchase: ' . $equity->name,
            // Use the provided purchase date or fallback to now.
            'transaction_date'     => $validatedData['purchased_at'] ?? now(),
        ]);

        // Redirect to the equities index with a success message.
        return redirect()->route('equities.index')
            ->with('success', 'Equity added successfully and transaction recorded!');
    }

    public function show(Equity $equity)
    {
        return view('equities.show', compact('equity'));
    }

    public function sell(Request $request, Equity $equity)
    {
        $user = Auth::user();

        // Validate the sale input.
        $data = $request->validate([
            'sell_price' => 'required|numeric',
            'sold_at'    => 'required|date',
            'shares'     => 'required|integer|min:1|max:' . $equity->quantity,
        ]);

        // Check if the equity has an associated account_id
        if (is_null($equity->account_id)) {
            return redirect()->back()->withErrors('No account associated with this equity.');
        }

        $sharesToSell = $data['shares'];
        // Calculate total sale value (number of shares to sell * selling price per share)
        $totalSaleValue = $sharesToSell * $data['sell_price'];

        // Create the associated transaction record.
        \App\Models\Transaction::create([
            'user_id'              => $user->id,
            'account_id'           => $equity->account_id,
            'transactionable_id'   => $equity->id,
            'transactionable_type' => \App\Models\Equity::class,
            'type'                 => 'credit', // A sale brings in funds.
            'amount'               => $totalSaleValue,
            'description'          => 'Sold ' . $sharesToSell . ' shares of ' . $equity->name . ' at $' . number_format($data['sell_price'], 8) . ' per share',
            'transaction_date'     => $data['sold_at'],
        ]);

        // If selling all shares, update and soft delete the equity.
        if ($sharesToSell == $equity->quantity) {
            $equity->update([
                'current_price'    => $data['sell_price'],
                'sold_at'          => $data['sold_at'],
                'transaction_type' => 'sell',
            ]);
            $equity->delete();
        } else {
            // For a partial sale, update the remaining quantity and total amount.
            $newQuantity = $equity->quantity - $sharesToSell;
            $newAmount   = $equity->purchase_price * $newQuantity;
            $equity->update([
                'quantity'         => $newQuantity,
                'amount'           => $newAmount,
                // Optionally, you can update current_price if you want to reflect the last sale price.
                'current_price'    => $data['sell_price'],
            ]);
        }

        return redirect()->route('equities.index')
                         ->with('success', 'Equity sale processed successfully and transaction recorded!');
    }

}
