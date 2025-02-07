<?php

namespace App\Http\Controllers;

use App\Models\AssetType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssetTypeController extends Controller
{

    public function show()
    {
        $user = Auth::user();

        $assetTypes = AssetType::where(function($query) use ($user)
        {
            $query->where('user_id', $user->id);

        })->get();

        return view('assets.types', compact('assetTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $user = Auth::user();
        $assetType = AssetType::withTrashed()->where('name', $validated['name'])->first();

        if ($assetType)
        {
            if ($assetType->trashed())
            {
                $assetType->restore();

                $assetType->update([
                    'user_id' => $user->id,
                ]);

                return redirect()->route('asset_types.show')->with('success', 'Asset type created successfully!');
            }
            else
            {
                return redirect()
                    ->route('asset_types.show')
                    ->with('error', 'Asset type already exists!');
            }
        }

        AssetType::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
        ]);

        return redirect()->route('asset_types.show')->with('success', 'Asset type created successfully!');
    }


    public function destroy(AssetType $assetType)
    {
        if ($assetType->user_id !== Auth::id())
        {
            abort(403, 'Unauthorized action.');
        }

        $assetType->delete();

        return redirect()->route('asset_types.show')->with('success', 'Asset Type deleted successfully!');

    }

    public function update(Request $request, AssetType $assetType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        if ($assetType->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $assetType->update($validated);

        return redirect()->back()->with('success', 'Asset Type updated successfully!');
    }

}
