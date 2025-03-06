<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Account;
use App\Models\Currency;
use App\Models\Liability;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LiabilityController extends Controller
{

    public function index(Request $request)
    {
        $query = Liability::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $query->orderBy('created_at', 'desc');

        $liabilities = $query->paginate(10);

        return view('liabilities.index', compact('liabilities'));
    }

    public function create()
    {
        $user = Auth::user();
        $currencies = Currency::where('user_id', $user->id)->get();
        return view('liabilities.create', compact('currencies'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'reference_number' => [
                'required',
                'string',
                Rule::unique('liabilities')->where(function ($query) use ($user, $request) {
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

        $currency = Currency::findorFail($request->currency_id);
        $status = 'pending';
        if ($request->due_date) {
            $dueDate = Carbon::parse($request->due_date, $user->timezone)->toDateString();
            $today   = Carbon::now($user->timezone)->toDateString();
            if ($dueDate < $today) {
                $status = 'overdue';
            }
        }
        $liability = Liability::create([
            'user_id'                   => $user->id,
            'currency_id'               => $request->currency_id,
            'currency_exchange_rate'    => $currency->exchange_rate,
            'reference_number'          => $request->reference_number,
            'name'                      => $request->name,
            'paid_amount'               => 0,
            'total_toBePaid'            => $request->total_toBePaid,
            'status'                    => $status,
            'due_date'                  => $request->due_date,
            'description'               => $request->description,
            'due_date'                  => $request->due_date,
            'paid_at'                   => null,
            'created_at'                => now(),
            'updated_at'                => now()
        ]);

        return redirect()->route('liabilities.index')->with('success', 'Liability created successfully.');
    }

    public function show($id)
    {
        $liability = Liability::findOrFail($id);
        return view('liabilities.detail', compact('liability'));
    }


}
