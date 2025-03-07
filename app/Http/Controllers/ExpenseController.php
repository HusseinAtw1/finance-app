<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
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
        $user = Auth::user();

        $request->validate([
            'reference_number' => [
                'required',
                'string',
                Rule::unique('expenses')->where(function ($query) use ($user, $request) {
                    return $query->where('user_id', $user->id)
                                 ->where('name', $request->name);
                }),
            ],
            'name'            => 'required|string|max:255',
            'currency_id'     => 'nullable|exists:currencies,id',
            'total_toBePaid'  => 'required|numeric|min:0',
            'due_date'        => 'nullable|date',
            'description'     => 'nullable|string',
        ]);

        $currency = Currency::findOrFail($request->currency_id);
        $status = 'pending';
        if ($request->due_date) {
            $dueDate = Carbon::parse($request->due_date, $user->timezone)->toDateString();
            $today   = Carbon::now($user->timezone)->toDateString();
            if ($dueDate < $today) {
                $status = 'overdue';
            }
        }

        $expense = Expense::create([
            'user_id'                => $user->id,
            'currency_id'            => $request->currency_id,
            'currency_exchange_rate' => $currency->exchange_rate,
            'reference_number'       => $request->reference_number,
            'name'                   => $request->name,
            'paid_amount'            => 0,
            'total_toBePaid'         => $request->total_toBePaid,
            'status'                 => $status,
            'due_date'               => $request->due_date,
            'description'            => $request->description,
            'paid_at'                => null,
            'created_at'             => now(),
            'updated_at'             => now()
        ]);

        return redirect()->route('expenses.index')->with('success', 'Expense created successfully.');
    }


    public function show($id)
    {
        $expense = Expense::findOrFail($id);
        return view('expenses.detail', compact('expense'));
    }

}
