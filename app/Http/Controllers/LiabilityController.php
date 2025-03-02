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

}
