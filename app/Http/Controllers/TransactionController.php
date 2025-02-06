<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function show(Request $request)
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
}
