<?php

namespace App\Http\Controllers;

use App\Models\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StorageController extends Controller
{

    public function index()
    {
        $storages = Storage::where('user_id', Auth::id())->get();
        return view('storages.storages', compact('storages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:storages,name,NULL,id,user_id,' . Auth::id(),
            'address' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $storage = new Storage();
        $storage->user_id = Auth::id();
        $storage->name = $request->name;
        $storage->address = $request->address;
        $storage->description = $request->description;
        $storage->save();

        return redirect()->back()->route('storages.storages')->with('success', 'Storage created successfully.');
    }

    public function update(Request $request, Storage $storage)
    {
        $this->authorize('update', $storage);

        $request->validate([
            'name' => 'required|string|max:255|unique:storages,name,' . $storage->id . ',id,user_id,' . Auth::id(),
            'address' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $storage->name = $request->name;
        $storage->address = $request->address;
        $storage->description = $request->description;
        $storage->save();

        return redirect()->back()->with('success', 'Storage updated successfully.');
    }

    public function destroy(Storage $storage)
    {
        $this->authorize('delete', $storage);

        $storage->delete();

        return redirect()->back()->with('success', 'Storage deleted successfully.');
    }
}
