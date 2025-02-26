<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');

        // Validate sort field to prevent SQL injection
        $allowedSortFields = ['id', 'name', 'phone_number', 'created_at'];
        if (!in_array($sort, $allowedSortFields)) {
            $sort = 'created_at';
        }

        $query = Customer::where('user_id', $user->id);

        // Apply search if provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy($sort, $direction)
                          ->paginate(10);

        if ($request->has('export')) {
            return $this->exportCustomers($query->get());
        }

        return view('customers.index', compact('customers', 'search', 'sort', 'direction'));
    }

    private function exportCustomers($customers)
    {
        $filename = 'customers_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($customers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Name', 'Phone Number', 'Created At']);

            foreach ($customers as $customer) {
                fputcsv($file, [
                    $customer->id,
                    $customer->name,
                    $customer->phone_number,
                    $customer->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
