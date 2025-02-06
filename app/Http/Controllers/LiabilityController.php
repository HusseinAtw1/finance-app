<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Liability;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LiabilityController extends Controller
{

    public function index(Request $request)
    {
        $user = Auth::user();

        // Update overdue liabilities for the authenticated user:
        // If a liability is pending and its due_date is in the past, mark it as overdue.
        Liability::where('user_id', $user->id)
            ->where('due_date', '<', now())
            ->where('status', 'pending')
            ->update(['status' => 'overdue']);

        // Get all accounts of type "liability" for this user.
        $accounts = Account::where('user_id', $user->id)
                    ->where('acc_type', 'liability')
                    ->get();

        // Determine the selected account from the query string or default to the first account's id.
        $selectedAccount = $request->query('account_id', $accounts->first()->id ?? null);

        // Start building the query for liabilities.
        $query = Liability::where('user_id', $user->id);

        // Filter by the selected account if provided.
        if ($selectedAccount) {
            $query->where('account_id', $selectedAccount);
        }

        // Get the status filter parameter (default to "all").
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

        // Order liabilities so that:
        // 1. Overdue liabilities appear first (ordered by earliest due_date)
        // 2. Upcoming liabilities follow, ordered by due_date ascending.
        $query->orderByRaw("CASE WHEN due_date < NOW() THEN 0 ELSE 1 END ASC, due_date ASC");

        // Paginate the results with the applied filters.
        $liabilities = $query->paginate(6)->appends([
            'account_id' => $selectedAccount,
            'status'     => $status,
            'search'     => $search
        ]);

        return view('liabilities.index', compact('liabilities', 'accounts', 'selectedAccount', 'status'));
    }


    public function create()
    {
        $user = Auth::user();
        $accounts = Account::where('user_id', $user->id)
                    ->where('acc_type', 'liability')
                    ->get();

        return view('liabilities.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_id'   => 'required|exists:accounts,id',
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'amount'       => 'required|numeric|min:0',
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

        // Create the liability record.
        $liability = Liability::create([
            'user_id'     => $user->id,
            'account_id'  => $request->account_id,
            'name'        => $request->name,
            'description' => $request->description,
            'amount'      => $request->amount,
            'due_date'    => $request->due_date,
            'status'      => $request->status,
            'paid_at'     => $request->paid_at,
            'paid_amount' => $request->paid_amount,
        ]);

        // Create a transaction for liability creation (credit entry).
        Transaction::create([
            'user_id'              => $user->id,
            'account_id'           => $request->account_id,
            'transactionable_id'   => $liability->id,
            'transactionable_type' => Liability::class,
            'type'                 => 'credit',
            'amount'               => $request->amount,
            'description'          => 'Liability Created: ' . $request->name,
            'transaction_date'     => now(),
        ]);

        // If a paid amount is provided, create a payment transaction (debit entry).
        if ($request->filled('paid_amount') && $request->paid_amount > 0) {
            Transaction::create([
                'user_id'              => $user->id,
                'account_id'           => $request->account_id,
                'transactionable_id'   => $liability->id,
                'transactionable_type' => Liability::class,
                'type'                 => 'debit',
                'amount'               => $request->paid_amount,
                'description'          => 'Liability Payment: ' . $request->name,
                'transaction_date'     => $request->paid_at ?? now(),
            ]);
        }

        return redirect()->route('liabilities.index')->with('success', 'Liability added successfully!');
    }


    public function pay(Liability $liability)
    {
        return view('liabilities.pay', compact('liability'));
    }

    public function payUpdate(Request $request, Liability $liability)
    {
        // Validate the incoming request.
        $validated = $request->validate([
            'paid_amount' => 'required|numeric|min:0.01',
            'paid_at'     => 'required|date',
        ]);

        // Retrieve the account associated with the liability.
        $account = Account::where('user_id', $liability->user_id)
                          ->findOrFail($liability->account_id);

        // Ensure the account balance is sufficient for this additional payment.
        if ($account->balance < $validated['paid_amount']) {
            return back()->withErrors([
                'paid_amount' => 'Insufficient account balance for the payment transaction.'
            ])->withInput();
        }

        // Get the current paid amount (if no previous payment, treat as zero).
        $currentPaid = $liability->paid_amount ?? 0;

        // Calculate the new cumulative total.
        $newTotalPaid = $currentPaid + $validated['paid_amount'];

        // Ensure the cumulative payment does not exceed the total liability amount.
        if ($newTotalPaid > $liability->amount) {
            return back()->withErrors([
                'paid_amount' => 'The cumulative payment cannot exceed the total liability amount.'
            ]);
        }

        // Determine the new status.
        $status = $newTotalPaid < $liability->amount ? 'pending' : 'paid';
        $paidAt = $newTotalPaid == $liability->amount ? $validated['paid_at'] : $liability->paid_at;

        // Use database transaction to ensure atomic update.
        DB::transaction(function () use ($liability, $validated, $newTotalPaid, $status, $paidAt) {
            // Update the liability record.
            $liability->update([
                'paid_amount' => $newTotalPaid,
                'paid_at'     => $paidAt,
                'status'      => $status,
            ]);

            // Create a corresponding transaction record (debit entry).
            Transaction::create([
                'user_id'              => $liability->user_id,
                'account_id'           => $liability->account_id,
                'transactionable_id'   => $liability->id,
                'transactionable_type' => Liability::class,
                'amount'               => $validated['paid_amount'],
                'type'                 => 'debit', // Payments reduce liabilities.
                'description'          => "Payment for liability: {$liability->name}",
                'transaction_date'     => $validated['paid_at'],
            ]);
        });

        return redirect()->route('liabilities.index')->with('success', 'Payment recorded successfully.');
    }



    public function show(Liability $liability)
    {
        return view('liabilities.show', compact('liability'));
    }

}
