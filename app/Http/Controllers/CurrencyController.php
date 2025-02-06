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
        $currencies = DB::table('currencies')->get();
        return  view('currencies.currencies', ['currencies' => $currencies]);
    }

    public function store(Request $request)
    {
         $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'size:3', Rule::unique('currencies')->where(fn ($query) => $query->where('user_id', $user->id)),],
            'full_name' => ['required', 'string'],
            'symbol' => ['nullable', 'string'],
        ]);

        $currency = Currency::create([
            'user_id'   => $user->id,
            'name'      => $validated['name'],
            'full_name' => $validated['full_name'],
            'symbol'    => $validated['symbol'] ?? null,
            'active'    => true,
        ]);

        return redirect()->back()->with('success', 'Currency created successfully!');
    }

    public function destroy(Currency $currency)
    {
        $currency->delete();
        return redirect()->back()->with('success', 'Currency deleted successfully!');
    }

}
