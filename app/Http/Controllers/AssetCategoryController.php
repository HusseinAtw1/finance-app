<?php

namespace App\Http\Controllers;

use App\Models\AssetCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssetCategoryController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        $assetCategories = AssetCategory::where(function($query) use ($user){
            $query->where('user_id', $user->id);
        })->get();

        return view('assets.categories', compact('assetCategories'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255']
        ]);

        $validated['name'] = ucfirst(strtolower($validated['name']));

        $assetCategory = AssetCategory::withTrashed()->where(function ($query) use ($user, $validated){
            $query->where('user_id', $user->id);
            $query->where('name', $validated['name']);
        })->first();

        if ($assetCategory)
        {
            if($assetCategory->trashed())
            {
                $assetCategory->restore();
                return back()->with('success', 'Asset category created successfully!');
            }
            else
            {
                return back()->with('error', 'Asset category already exists!');
            }
        }

        AssetCategory::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
        ]);

        return back()->with('success', 'Asset category created successfully!');
    }

    public function update(Request $request, AssetCategory $assetCategory)
    {
        $user = Auth::user();
        $this->authorize('update', $assetCategory);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $validated['name'] = ucfirst(strtolower($validated['name']));

        $assetCategoryCheck = AssetCategory::withTrashed()->where(function ($query) use($user, $validated){
            $query->where('user_id', $user->id);
            $query->where('name', $validated['name']);
        })->first();

        if($assetCategoryCheck)
        {
            if($assetCategoryCheck->trashed())
            {
                $assetCategoryCheck->restore();
                $this->authorize('delete', $assetCategory);
                $assetCategory->delete();
                return back()->with('success', 'Asset category updated successfully!');
            }
            else
            {
                return back()->with('error', 'Asset category already exists!');
            }
        }

        $assetCategory->update($validated);

        return back()->with('success', 'Asset category updated successfully!');
    }

    public function destroy(AssetCategory $assetCategory)
    {
        $this->authorize('delete', $assetCategory);

        $assetCategory->delete();

        return back()->with('success', 'Asset category deleted successfully!');
    }


}
