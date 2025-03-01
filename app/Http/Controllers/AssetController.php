<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Asset;
use App\Models\Account;
use App\Models\Storage;
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

        // Get filter inputs from the request; note no default value for status now.
        $status   = $request->input('status');
        $search   = $request->input('search');
        $category = $request->input('category');
        $type     = $request->input('type');
        $storage  = $request->input('storage');

        // Build the query using the provided filters
        $assets = Asset::with('assetStatus')
            ->where('user_id', $user->id)
            // Filter by status if provided using the assetStatus relationship
            ->when($status, function ($query, $status) {
                return $query->whereHas('assetStatus', function ($q) use ($status) {
                    $q->where('id', $status);
                });
            })
            // Filter by category if provided
            ->when($category, function ($query, $category) {
                return $query->where('asset_category_id', $category);
            })
            // Filter by type if provided
            ->when($type, function ($query, $type) {
                return $query->where('asset_type_id', $type);
            })
            // Filter by storage if provided
            ->when($storage, function ($query, $storage) {
                return $query->where('storage_id', $storage);
            })
            // Search by asset name or notes if provided
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('notes', 'like', "%{$search}%");
                });
            })
            ->paginate(9);

        // Load the data for dropdown filters
        $statuses   = AssetStatus::all();
        $categories = AssetCategory::where('user_id', $user->id)->get();
        $types      = AssetType::where('user_id', $user->id)->get();
        $storages   = Storage::where('user_id', $user->id)->get();

        // Pass all the data to the view
        return view('assets.assets', compact(
            'assets',
            'status',
            'search',
            'statuses',
            'categories',
            'types',
            'storages',
            'category',
            'type',
            'storage'
        ));
    }

    public function detail($id)
    {
        $asset = Asset::findOrFail($id);
        $currencies = Currency::all();
        return view('assets.detail', compact('asset', 'currencies'));
    }


}
