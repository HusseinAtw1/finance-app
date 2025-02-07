<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function create(Request $request)
    {
        $query = Account::where('user_id', Auth::id())
                        ->where('active', 1);

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('acc_type', $request->type);
        }

        $accounts = $query->get();

        return view('accs.create-acc', compact('accounts'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name'         => 'required|string|max:255',
            'account_type' => 'required|string|in:asset,liability,equity,revenue,expense',
            'balance'      => 'required|numeric',
        ]);

        $user = Auth::user();
        Account::create([
            'user_id'  => $user->id,
            'name'     => $validatedData['name'],
            'acc_type' => $validatedData['account_type'],
            'balance'  => $validatedData['balance'],
        ]);

        return redirect()->route('create.acc.show')->with('success', 'Account created successfully!');
    }

    public function destroy(Account $account)
    {
        if ($account->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $account->active = 0;
        $account->save();

        return redirect()->route('create.acc.show')->with('success', 'Account deactivated successfully!');
    }

    public function addBalance(Request $request, Account $account)
    {
        $request->validate([
            'amount' => 'required|numeric',
        ]);

        if ($account->user_id !== Auth::id())
        {
            abort(403, 'Unauthorized action.');
        }

        $account->balance += $request->amount;
        $account->save();

        return redirect()->route('create.acc.show')->with('success', 'Balance added successfully!');
    }
}
