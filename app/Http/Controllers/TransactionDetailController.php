<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Customer;
use App\Models\Liability;
use App\Models\AssetStatus;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class TransactionDetailController extends Controller
{
    public function buyAsset(Request $request, Transaction $transaction)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'              => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request, $user) {
                    $exists = Asset::where('user_id', $user->id)
                        ->where('name', $value)
                        ->where('reference_number', $request->reference_number)
                        ->exists();
                    if ($exists) {
                        $fail('An asset with this name and reference number already exists.');
                    }
                }],
            'reference_number'  => 'required|string|max:100',
            'asset_category'    => 'required|exists:asset_categories,id',
            'asset_type'        => 'required|exists:asset_types,id',
            'currency_id'       => 'required|exists:currencies,id',
            'depreciation_id'   => 'nullable|exists:asset_depreciations,id',
            'current_value'     => 'required|numeric|min:0',
            'purchase_price'    => 'required|numeric|min:0',
            'purchase_date'     => 'required|date',
            'storage_id'        => ['required', 'integer', Rule::exists('storages', 'id')->where('user_id', $user->id),],
            'supplier_id'       => 'required|exists:suppliers,id',
            'notes'             => 'nullable|string',
            'account_id'        => ['required', Rule::exists('accounts', 'id')->where('user_id', $user->id),],
            'quantity'          => 'required|integer|min:1',
        ]);

        $status = AssetStatus::where('name', 'Pending')->first();

        $asset = Asset::create([
            'user_id'               => $user->id,
            'reference_number'      => $validated['reference_number'],
            'currency_id'           => $validated['currency_id'],
            // 'currency_exchange_rate'=> $request->currency_exchange_rate ?? null,
            'asset_type_id'         => $validated['asset_type'],
            'asset_category_id'     => $validated['asset_category'],
            'asset_status_id'       => $status->id,
            'asset_depreciation_id' => $validated['depreciation_id'],
            'name'                  => $validated['name'],
            'quantity'              => $validated['quantity'],
            'current_value'         => $validated['current_value'],
            'purchase_price'        => $validated['purchase_price'],
            'storage_id'            => $validated['storage_id'] ?? null,
            'notes'                 => $validated['notes'] ?? null,
            'purchase_at'           => $validated['purchase_date'],
            'created_at'            => now(),
            'updated_at'            => now(),
        ]);

        $transactionDetail = TransactionDetail::create([
            'transaction_id'        => $transaction->id,
            'transactionable_type'  => Asset::class,
            'transactionable_id'    => $asset->id,
            'account_id'            => $request->account_id,
            'supplier_id'           => $request->supplier_id,
            'customer_id'           => null,
            'type'                  => 'debit',
            'current_price'         => $request->current_value,
            'purchase_price'        => $request->purchase_price,
            'sold_for'              => null,
            'quantity'              => $request->quantity,
            'amount'                => $request->purchase_price * $request->quantity,
            'created_at'            => now(),
            'updated_at'            => now(),
        ]);

        return redirect()->back();

    }

    public function sellAsset(Request $request, Transaction $transaction)
    {
        $user = Auth::user();

        $request->validate([
            'asset_id' => ['required', 'integer', Rule::exists('assets', 'id')],
            'account_id' => ['required', 'integer', Rule::exists('accounts', 'id')],
            'quantity' => ['required', 'integer', 'min:1',
                function ($attribute, $value, $fail) use ($request) {
                    $asset = Asset::find($request->asset_id);
                    if ($value > $asset->quantity) {
                        $fail("The requested quantity ({$value}) exceeds the available stock ({$asset->quantity}).");
                    }
                }],
            'customer'   => ['required', 'string', 'max:255'],
            'customer_number' => ['required', 'string', 'max:50'],
            'sold_for'      => ['required', 'numeric', 'min:0'],
            'sold_at'       => ['required', 'date'],
        ]);

        $customer = Customer::where('user_id', $user->id)->where('name', $request->customer)->where('phone_number', $request->customer_number)->first();

        if(!$customer) $customer = Customer::Create(['user_id' => $user->id, 'name' => $request->customer, 'phone_number' => $request->customer_number, 'created_at' => now(), 'updated_at' => now()]);

        $asset = Asset::findOrFail($request->asset_id);

        $asset->quantity -= $request->quantity;

        if($asset->quantity === 0)
        {
            $status = AssetStatus::where('name', 'Sold')->firstOrFail();

            $asset->asset_status_id = $status->id;
        }

        $asset->save();

        TransactionDetail::create([
            'transaction_id'        => $transaction->id,
            'transactionable_type'  => Asset::class,
            'transactionable_id'    => $request->asset_id,
            'account_id'            => $request->account_id,
            'supplier_id'           => null,
            'customer_id'           => $customer->id,
            'type'                  => 'credit',
            'current_price'         => $asset->current_value,
            'purchase_price'        => $asset->purchase_price,
            'sold_for'              => $request->sold_for,
            'quantity'              => $request->quantity,
            'amount'                => $request->sold_for * $request->quantity,
            'created_at'            => now(),
            'updated_at'            => now(),
        ]);

        return back();
    }

    public function destroy($id)
    {
        try {
            $transactionDetail = TransactionDetail::findOrFail($id);

            $this->authorize('delete', $transactionDetail);

            $transactionDetail->forceDelete();

            if ($transactionDetail->transactionable_type === Asset::class && $transactionDetail->type === 'debit') {

                $asset = Asset::findOrFail($transactionDetail->transactionable_id);

                $asset->forceDelete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Transaction detail deleted successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to delete transaction detail: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete transaction detail: ' . $e->getMessage()
            ], 500);
        }
    }

    public function payLiability(Request $request, Transaction $transaction)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'liability_id' => ['required', 'integer', Rule::exists('liabilities', 'id')->where('user_id', $user->id)],
            'account_id'   => ['required', 'integer', Rule::exists('accounts', 'id')->where('user_id', $user->id)],
            'paid_amount'  => ['required', 'numeric', 'min:0.01'],
            'paid_at'      => ['required', 'date'],
        ]);

        $liability = Liability::findOrFail($validated['liability_id']);

        TransactionDetail::create([
            'transaction_id'        => $transaction->id,
            'transactionable_type'  => Liability::class,
            'transactionable_id'    => $liability->id,
            'account_id'            => $validated['account_id'],
            'supplier_id'           => null,
            'customer_id'           => null,
            'currency_id'           => $liability->currency_id,
            'currency_exchange_rate'=> $liability->currency_exchange_rate,
            'type'                  => 'debit',
            'current_price'         => $validated['paid_amount'],
            'purchase_price'        => $validated['paid_amount'],
            'sold_for'              => null,
            'quantity'              => 1,
            'amount'                => $validated['paid_amount'],
            'paid_at'               => $validated['paid_at'],
            'created_at'            => now(),
            'updated_at'            => now(),
        ]);

        return redirect()->back()->with('success', 'Liability payment processed successfully.');
    }

}
