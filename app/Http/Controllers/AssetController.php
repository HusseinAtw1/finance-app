<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Asset;
use App\Models\Account;
use App\Models\Currency;
use App\Models\AssetType;
use App\Models\AssetStatus;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\AssetCategory;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Date;


class AssetController extends Controller
{
    public function show(Request $request)
    {
        $user = Auth::user();

        $accounts = Account::where('user_id', $user->id)->where('acc_type', 'asset')->get();

        $selectedAccount = $request->input('account_id', $accounts->first()->id ?? null);

        $status = $request->input('status', 'owned');

        $search = $request->input('search');

        $assets = Asset::where('user_id', $user->id)->when($selectedAccount, function ($query, $selectedAccount)
            {
                return $query->where('account_id', $selectedAccount);
            })->when($status === 'sold', function ($query)
            {
                return $query->whereHas('assetStatus', function ($q)
                {
                    $q->where('name', 'Sold');
                });
            })->when($status === 'owned', function ($query)
            {
                return $query->whereDoesntHave('assetStatus', function ($q)
                {
                    $q->where('name', 'Sold');
                });
            })
            ->when($search, function ($query, $search)
            {
                return $query->where(function ($q) use ($search)
                {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('notes', 'like', "%{$search}%");
                });
            })
            ->paginate(9);

        return view('assets.assets', compact('assets', 'accounts', 'selectedAccount', 'status', 'search'));
    }



    public function create()
    {
        $user = Auth::user();
        $accs = Account::where('user_id', $user->id)
                    ->where('acc_type', 'asset')
                    ->get();
        $currencies = Currency::all();
        $assetStatuses = AssetStatus::all();
        $assetCategories = AssetCategory::all();
        $assetTypes = AssetType::all();

        return view('assets.create', compact('accs', 'currencies', 'assetStatuses', 'assetCategories', 'assetTypes'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $status = AssetStatus::find($request->status);

        $request->validate([
            'account_id'     => 'required|exists:accounts,id',
            'name'           => 'required|string|max:255',
            'location'       => 'required|string|max:255',
            'purchase_date' => ['required', 'date', 'before:' . Carbon::now()->setTimezone($user->timezone)->format('Y-m-d H:i:s')],
            'current_value'  => 'required|numeric|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'category'       => ['required', 'integer', Rule::exists('asset_categories', 'id')],
            'status'         => ['required', 'integer', Rule::exists('asset_statuses', 'id')],
            'type'           => ['required', 'integer', Rule::exists('asset_types', 'id')],
            'currency'       => ['required', 'integer', Rule::exists('currencies', 'id')],
            'quantity'       => 'required|integer|min:1',
            'notes'          => 'nullable|string',
            'sold_for'       => ['nullable', 'numeric', 'min:0', Rule::requiredIf($status && $status->name === 'Sold')],
            'sold_at'        => ['nullable', 'date', Rule::requiredIf($status && $status->name === 'Sold')],
        ]);

        $purchaseDate = Carbon::parse($request->purchase_date, $user->timezone)->setTimezone('UTC');

        Asset::create([
            'user_id'        => $user->id,
            'account_id'     => $request->account_id,
            'currency_id'    => $request->currency,
            'asset_type_id'  => $request->type,
            'asset_category_id'=> $request->category,
            'asset_status_id'=> $request->status,
            'name'           => $request->name,
            'quantity'       => $request->quantity,
            'current_value'  => $request->current_value,
            'purchase_price' => $request->purchase_price,
            'location'       => $request->location,
            'notes'          => $request->notes,
            'purchase_at'    => $purchaseDate,
            'created_at'     => now(),
        ]);

        Transaction::create([
            'user_id'               =>  $user->id,
            'account_id'            =>  $request->account_id,
            'transactionable_type'  =>  Asset::class,
            'status'                =>  'completed',
            'type'                  =>  'debit',
            'amount'                =>  $request->quantity * $request->current_value,
            'description'           =>  'Bought '.$request->quantity.' of '.$request->name.' for '.$request->purchase_price,
        ]);

        if($status && $status->name === 'Sold')
        {
            Transaction::create([
                'user_id'               =>  $user->id,
                'account_id'            =>  $request->account_id,
                'transactionable_type'  =>  Asset::class,
                'status'                =>  'completed',
                'type'                  =>  'credit',
                'amount'                =>  $request->quantity * $request->sold_for,
                'transaction_date'      =>  $request->sold_at,
                'description'           =>  'Sold '.$request->quantity.' of '.$request->name.' for '.$request->sold_for,
            ]);
        }

        $transactionAmount = $request->purchase_price ?? $request->current_value;

        $account = Account::find($request->account_id);
        if ($account) {
            $account->balance -= $transactionAmount;
            $account->save();
        }

        return redirect()->route('assets.show')->with('success', 'Asset added successfully and transaction recorded!');
    }

    public function detail($id)
    {
        $asset = Asset::findOrFail($id);
        return view('assets.detail', compact('asset'));
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
