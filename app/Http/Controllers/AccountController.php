<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    // Display the create form and account details (only active accounts)
    public function create(Request $request)
    {
        // Build the query for the authenticated user's active accounts
        $query = Account::where('user_id', Auth::id())
                        ->where('active', 1);

        // If a type filter is provided and it's not "all", apply it
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('acc_type', $request->type);
        }

        // Retrieve the accounts from the query
        $accounts = $query->get();

        return view('accs.create-acc', compact('accounts'));
    }


    // Store a new account
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name'         => 'required|string|max:255',
            'account_type' => 'required|string|in:asset,liability,equity,revenue,expense',
            'balance'      => 'required|numeric',
        ]);

        // Create the new account for the current authenticated user
        $user = Auth::user();
        Account::create([
            'user_id'  => $user->id,
            'name'     => $validatedData['name'],
            'acc_type' => $validatedData['account_type'], // note: column renamed to `acc_type` per your schema
            'balance'  => $validatedData['balance'],
        ]);

        return redirect()->route('create.acc.show')->with('success', 'Account created successfully!');
    }

    // "Delete" an account by deactivating it (setting active to 0)
    public function destroy(Account $account)
    {
        // Ensure the authenticated user owns this account
        if ($account->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Instead of deleting, set active to 0 (deactivate the account)
        $account->active = 0;
        $account->save();

        return redirect()->route('create.acc.show')->with('success', 'Account deactivated successfully!');
    }

    // Add balance to an account
    public function addBalance(Request $request, Account $account)
    {
        // Validate the amount to add
        $request->validate([
            'amount' => 'required|numeric',
        ]);

        // Ensure the authenticated user owns this account
        if ($account->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Add the provided amount to the current balance
        $account->balance += $request->amount;
        $account->save();

        return redirect()->route('create.acc.show')->with('success', 'Balance added successfully!');
    }
}
