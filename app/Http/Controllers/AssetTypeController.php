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

        $validated['name'] = ucfirst(strtolower($validated['name']));

        $user = Auth::user();
        $assetType = AssetType::withTrashed()
        ->where('name', $validated['name'])
        ->where('user_id', $user->id)
        ->first();

        if ($assetType)
        {
            if ($assetType->trashed())
            {
                $assetType->restore();

                return redirect()->route('asset_types.show')->with('success', 'Asset type created successfully!');
            }
            else
            {
                return redirect()->route('asset_types.show')->with('error', 'Asset type already exists!');
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
        $this->authorize('delete', $assetType);

        $assetType->delete();

        return redirect()->route('asset_types.show')->with('success', 'Asset Type deleted successfully!');

    }

    public function update(Request $request, AssetType $assetType)
    {
        $this->authorize('update', $assetType);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $validated['name'] = ucfirst(strtolower($validated['name']));

        $user = Auth::user();

        $assetTypeCheck = AssetType::withTrashed()->where(function ($query) use ($user, $validated){
            $query->where('user_id', $user->id);
            $query->where('name', $validated['name']);
        })->first();

        if($assetTypeCheck)
        {
            if($assetTypeCheck->trashed())
            {
                $assetTypeCheck->restore();
                $this->authorize('delete', $assetType);
                $assetType->delete();
                return back()->with('success', 'Asset type updated successfully!');
            }
            else
            {
                return back()->with('error', 'Asset type already exists!');
            }
        }

        $assetType->update($validated);

        return redirect()->back()->with('success', 'Asset Type updated successfully!');
    }

}
