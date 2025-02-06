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
        $user = Auth::user();

        // Mark overdue expenses:
        // For any expense that has a due date, is not paid, and whose due date is in the past,
        // update its status to 'overdue'.
        Expense::where('user_id', $user->id)
            ->where('status', '!=', 'paid')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->update(['status' => 'overdue']);

        // Retrieve all accounts for the user to populate the account filter.
        $accounts = Account::where('user_id', $user->id)->get();

        // Start building the query for expenses.
        $query = Expense::where('user_id', $user->id);

        // Filter by account if provided.
        if ($account_id = $request->query('account_id')) {
            $query->where('account_id', $account_id);
        }

        // Filter by status if provided (pending, overdue, paid).
        // Default is "all" meaning no status filtering.
        $status = $request->query('status', 'all');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Search by name or description if provided.
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Order expenses so that:
        // - Expenses with a due date in the past and not paid (i.e. overdue) come first,
        //   ordered by the earliest due date (closest to or most overdue).
        // - Then, order by due_date (if available) ascending, and finally by created_at descending.
        $query->orderByRaw("CASE WHEN due_date IS NOT NULL AND due_date < NOW() AND status != 'paid' THEN 0 ELSE 1 END, due_date ASC, created_at DESC");

        // Paginate the results with filters applied (6 per page in this example).
        $expenses = $query->paginate(6)->appends([
            'account_id' => $account_id,
            'status'     => $status,
            'search'     => $search,
        ]);

        return view('expenses.index', compact('expenses', 'accounts'));
    }


    public function create()
    {
        $user = Auth::user();
        $accounts = Account::where('user_id', $user->id)
                    ->where('acc_type', 'expense')
                    ->get();
        $currencies = Currency::all();

        return view('expenses.create', compact('accounts', 'currencies'));
    }

    public function store(Request $request)
    {
        // Validate request data
        $request->validate([
            'account_id'   => 'required|exists:accounts,id',
            'name'         => 'required|string|max:255',
            'currency'     => 'required|string|size:3',
            'amount'       => 'required|numeric|min:0.01',
            'description'  => 'nullable|string',
            'category'     => 'required|string|max:255',
            'due_date'     => 'nullable|date',
            'status'       => 'required|in:pending,paid,overdue',
            'paid_at'      => 'nullable|date',
            'paid_amount'  => 'nullable|numeric|min:0',
        ]);

        $user = Auth::user();

        // Retrieve the account and validate that it belongs to the user.
        $account = Account::where('user_id', $user->id)
                          ->findOrFail($request->account_id);

        // If a paid amount is provided, ensure the account has sufficient balance.
        if ($request->filled('paid_amount') && $request->paid_amount > 0) {
            if ($account->balance < $request->paid_amount) {
                return back()->withErrors([
                    'paid_amount' => 'Insufficient account balance for the payment transaction.'
                ])->withInput();
            }
        }

        // Determine the expense status.
        // If a paid amount is provided and it is equal or greater than the expense amount,
        // mark the expense as paid.
        $finalStatus = $request->status;
        if ($request->filled('paid_amount') && $request->paid_amount >= $request->amount) {
            $finalStatus = 'paid';
        }

        // Create the expense record.
        $expense = Expense::create([
            'user_id'      => $user->id,
            'account_id'   => $request->account_id,
            'name'         => $request->name,
            'currency'     => strtoupper($request->currency),
            'amount'       => $request->amount,
            'paid_amount'  => $request->paid_amount,
            'description'  => $request->description,
            'category'     => $request->category,
            'due_date'     => $request->due_date,
            'status'       => $finalStatus,
            'paid_at'      => $request->paid_at,
        ]);

        // Create a transaction for the expense creation (debit entry).
        Transaction::create([
            'user_id'              => $user->id,
            'account_id'           => $request->account_id,
            'transactionable_id'   => $expense->id,
            'transactionable_type' => Expense::class,
            'type'                 => 'debit',
            'amount'               => $request->amount,
            'description'          => 'Expense Created: ' . $request->name,
            'transaction_date'     => now(),
        ]);

        // If a paid amount is provided, create a payment transaction (credit entry)
        // and subtract the payment from the account balance.
        if ($request->filled('paid_amount') && $request->paid_amount > 0) {
            // Create payment transaction
            Transaction::create([
                'user_id'              => $user->id,
                'account_id'           => $request->account_id,
                'transactionable_id'   => $expense->id,
                'transactionable_type' => Expense::class,
                'type'                 => 'credit',
                'amount'               => $request->paid_amount,
                'description'          => 'Expense Payment: ' . $request->name,
                'transaction_date'     => $request->paid_at ?? now(),
            ]);

            // Subtract the paid amount from the account balance.
            $account->balance -= $request->paid_amount;
            $account->save();
        }

        return redirect()->route('expenses.index')
                         ->with('success', 'Expense added successfully!');
    }

    public function show(Expense $expense)
    {
        return view('expenses.show', compact('expense'));
    }

    public function pay(Request $request, Expense $expense)
    {
        // Prevent additional payments if the expense is already fully paid.
        if ($expense->paid_amount >= $expense->amount) {
            return redirect()->back()->withErrors([
                'paid_amount' => 'This expense is already fully paid.'
            ])->withInput();
        }

        // Validate the payment request data
        $request->validate([
            'paid_amount' => 'required|numeric|min:0.01',
            'paid_at'     => 'required|date',
        ]);

        $user = Auth::user();

        // Ensure the expense belongs to the authenticated user by checking the associated account
        $account = Account::where('user_id', $user->id)
                          ->findOrFail($expense->account_id);

        // Check if the account has sufficient balance for the payment
        if ($account->balance < $request->paid_amount) {
            return back()->withErrors([
                'paid_amount' => 'Insufficient account balance for the payment transaction.'
            ])->withInput();
        }

        // Subtract the payment from the account balance.
        $account->balance -= $request->paid_amount;
        $account->save();

        // Update the expense's paid_amount by adding the new payment.
        $expense->paid_amount = ($expense->paid_amount ?? 0) + $request->paid_amount;

        // Update the expense status to 'paid' if the full amount has been met or exceeded.
        if ($expense->paid_amount >= $expense->amount) {
            $expense->status = 'paid';
        }

        // Record the date/time of this payment
        $expense->paid_at = $request->paid_at;
        $expense->save();

        // Create a transaction for the expense payment (credit entry)
        Transaction::create([
            'user_id'              => $user->id,
            'account_id'           => $account->id,
            'transactionable_id'   => $expense->id,
            'transactionable_type' => Expense::class,
            'type'                 => 'credit',
            'amount'               => $request->paid_amount,
            'description'          => 'Expense Payment: ' . $expense->name,
            'transaction_date'     => $request->paid_at ?? now(),
        ]);

        return redirect()->route('expenses.show', $expense->id)
                         ->with('success', 'Payment successful!');
    }
}
