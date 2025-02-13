<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CurrencyController extends Controller
{
    public function index(): View
    {
        $currencies = Currency::withoutTrashed()->get();
        return  view('currencies.currencies', ['currencies' => $currencies]);
    }

    public function store(Request $request)
    {
         $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'size:3', Rule::unique('currencies')->where(fn ($query) => $query->where('user_id', $user->id)),],
            'full_name' => ['required', 'string'],
            'symbol' => ['nullable', 'string'],
            'exchange_rate' => ['required', 'numeric'],
        ]);

        $validated['name'] = strtoupper($validated['name']);

        Currency::create([
            'user_id'   => $user->id,
            'name'      => $validated['name'],
            'full_name' => $validated['full_name'],
            'symbol'    => $validated['symbol'] ?? null,
            'exchange_rate' => $validated['exchange_rate'],
        ]);

        return redirect()->back()->with('success', 'Currency created successfully!');
    }

    public function destroy(Currency $currency)
    {
        $this->authorize('destroy', $currency);
        $currency->delete();
        return redirect()->back()->with('success', 'Currency deleted successfully!');
    }

    public function update(Request $request, Currency $currency)
    {
        $this->authorize('update', $currency);

        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'size:3'],
            'full_name' => ['required', 'string'],
            'symbol' => ['nullable', 'string'],
            'exchange_rate' => ['required', 'numeric'],
        ]);

        $validated['name'] = strtoupper($validated['name']);

        $currencyCheck = Currency::withTrashed()->where(function ($query) use ($user, $validated, $currency)
        {
            $query->where('user_id', $user->id);
            $query->where('name', $validated['name']);
            $query->where('id', '<>', $currency->id);
        })->first();

        if ($currencyCheck)
        {
            if ($currencyCheck->trashed())
            {
                $currencyCheck->restore();
                $this->authorize('destroy', $currency);
                $currency->delete();
                return back()->with('success', 'Restored a deleted currency with same name deleted old one!');
            }
            else
            {
                return back()->with('error', 'Currency already exists!');
            }
        }

        $currency->update($validated);
        return back()->with('success', 'Currency updated successfully!');
    }

}
