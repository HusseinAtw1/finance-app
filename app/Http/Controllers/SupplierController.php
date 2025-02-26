<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{

    public function index()
    {
        $suppliers = Supplier::where('user_id', Auth::id())->get();
        return view('suppliers.suppliers', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('suppliers')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                })
            ],
            'phone_number' => ['required', 'integer', 'max:20'],
        ]);

        try {
            Supplier::create([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'phone_number' => $request->phone_number,
            ]);

            return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error creating supplier: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('suppliers')->where(function ($query) use ($id) {
                    return $query->where('user_id', Auth::id())
                                ->where('id', '!=', $id);
                })
            ],
            'phone_number' => ['required', 'integer', 'max:20'],
        ]);

        $existingSupplier = Supplier::where('user_id', Auth::id())
            ->where('id', '!=', $id)
            ->where('name', $request->name)
            ->where('phone_number', $request->phone_number)
            ->first();

        if ($existingSupplier) {
            return redirect()->back()
                ->with('error', 'A supplier with this name and phone number combination already exists.')
                ->withInput();
        }

        try {
            $supplier->update([
                'name' => $request->name,
                'phone_number' => $request->phone_number,
            ]);

            return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating supplier: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $supplier = Supplier::where('user_id', Auth::id())->findOrFail($id);

        try {
            $supplier->delete();
            return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting supplier: ' . $e->getMessage());
        }
    }
}
