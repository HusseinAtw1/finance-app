<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Expense;
use App\Models\Currency;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $query->orderBy('created_at', 'desc');

        $expenses = $query->paginate(10);

        return view('expenses.index', compact('expenses'));
    }

    public function create()
    {
        $user = Auth::user();
        $currencies = Currency::where('user_id', $user->id)->get();
        return view('expenses.create', compact('currencies'));
    }

    public function store(Request $request)
    {

    }

    public function show($id)
    {
        $expense = Expense::findOrFail($id);
        return view('liabilities.detail', compact('expense'));
    }

}
