<?php

namespace App\Http\Controllers;

use App\Models\AssetStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class AssetStatusController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        $statuses = AssetStatus::where(function($query) use ($user)
        {
            $query->where('user_id', $user->id);
        })->get();

        return view('assets.status', compact('statuses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $validated['name'] = ucfirst(strtolower($validated['name']));

        $user = Auth::user();
        $assetStatus = AssetStatus::withTrashed()->where(function($query) use ($user, $validated) {
            $query->where('name', $validated['name']);
            $query->where('user_id', $user->id);
        })->first();

        if($assetStatus)
        {
            if($assetStatus->trashed())
            {
                $assetStatus->restore();

                return redirect()->route('asset_statuses.show')->with('success', 'Asset status create successfully!');
            }
            else
            {
                return redirect()->route('asset_statuses.show')->with('error', 'Asset type already exists!');
            }
        }
        AssetStatus::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
        ]);

        return redirect()->route('asset_statuses.show')->with('success', 'Asset status create successfully!');
    }


    public function destroy(AssetStatus $assetStatus)
    {
        $this->authorize('delete', $assetStatus);

        $assetStatus->delete();

        return redirect()->route('asset_statuses.show')->with('success', 'Asset status delete successfully!');
    }

    public function update(Request $request, AssetStatus $assetStatus)
    {
        $this->authorize('update', $assetStatus);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $assetStatus->update($validated);

        return redirect()->route('asset_statuses.show')->with('success', 'Asset status edited successfully!');
    }
}
