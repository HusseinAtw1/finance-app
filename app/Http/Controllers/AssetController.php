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

class AssetController extends Controller
{
    public function show(Request $request)
    {
        $user = Auth::user();

        $accounts = Account::where('user_id', $user->id)->where('acc_type', 'asset')->get();

        $selectedAccount = $request->input('account_id', $accounts->first()->id ?? null);

        $status = $request->input('status', 'owned');

        $search = $request->input('search');

        $assets = Asset::with('assetStatus')->where('user_id', $user->id)->when($selectedAccount, function ($query, $selectedAccount)
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

        $accs = Account::where('user_id', $user->id)->where('acc_type', 'asset')->get();

        $currencies = Currency::all();

        $assetStatuses = AssetStatus::all();

        $assetCategories = AssetCategory::all();

        $assetTypes = AssetType::all();

        return view('assets.create', compact('accs', 'currencies', 'assetStatuses', 'assetCategories', 'assetTypes'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $request['reference_number'] = strtoupper($request['reference_number']);
        $request->validate([
            'reference_number' => 'required|string|max:255',
            'name'           => 'required|string|max:255',
            'category'       => ['required', 'integer', Rule::exists('asset_categories', 'id')],
            'type'           => ['required', 'integer', Rule::exists('asset_types', 'id')],
            'notes'          => 'nullable|string',
        ]);

        $status = AssetStatus::where('name', 'Pending')->firstOrFail();

        Asset::create([
            'user_id'           => $user->id,
            'reference_number'  => $request->reference_number,
            'asset_type_id'     => $request->type,
            'asset_category_id' => $request->category,
            'asset_status_id'   => $status->id,
            'name'              => $request->name,
            'notes'             => $request->notes,
            'created_at'        => now(),
            'updated_at'         => now(),
        ]);

        return redirect()->route('assets.show')->with('success', 'Asset added successfully!');
    }

    public function detail($id)
    {
        $asset = Asset::findOrFail($id);
        $currencies = Currency::all();
        return view('assets.detail', compact('asset', 'currencies'));
    }

    public function sell(Request $request, $id)
    {
        $user = Auth::user();

        $asset = Asset::findOrFail($id);

        $validated = $request->validate([
            'sell_price' => ['required', 'numeric', 'min:0'],
            'sold_at'    => ['required', 'date', 'before:' . Carbon::now()->setTimezone($user->timezone)->format('Y-m-d H:i:s')],
            'account_id' => ['required', 'exists:accounts,id'],
            'currency'   => ['required', 'string', Rule::exists('currencies', 'id')],
            'quantity'   => ['required', 'integer', 'min:0', 'max:' . $asset->quantity],
        ]);

        $status = AssetStatus::where('name', 'Sold')->first();

        $quantity = $asset->quantity - $validated['quantity'];

        $asset->update([
            'quantity' => $quantity,
            'asset_status_id'   => $quantity === 0 ? $status->id : $asset->asset_status_id,
        ]);

        $transactionAmount = $validated['sell_price'] * $validated['quantity'];

        $currency = Currency::find($validated['currency']);

        Transaction::create([
            'user_id'              => $user->id,
            'account_id'           => $validated['account_id'],
            'transactionable_id'   => $asset->id,
            'transactionable_type' => Asset::class,
            'status'               => 'completed',
            'amount'               => $transactionAmount,
            'type'                 => 'credit',
            'description'          => "Sold asset: {$asset->name}, Quantity: {$validated['quantity']}, Price: {$validated['sell_price']}, Currency: {$currency->name}" . ($currency->symbol ? " ({$currency->symbol})" : ""),
            'transaction_date'     => $validated['sold_at'],
        ]);

        $account = Account::find($validated['account_id']);

        if ($account) {
            $account->balance += $transactionAmount;
            $account->save();
        }

        return redirect()->route('assets.show', $id)->with('success', 'Asset sold successfully!');
    }
}
