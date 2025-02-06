@extends('welcome')

@section('content')
<div class="container mt-4">
    <h2>Create an Account</h2>

    {{-- Display success messages --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Account creation form --}}
    <form action="{{ route('create-acc') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Account Name</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Enter account name" required>
        </div>

        {{-- Toggle bar for account types --}}
        <div class="mb-3">
            <label class="form-label d-block">Account Type</label>
            <div class="btn-group" role="group" aria-label="Account Type">
                <input type="radio" class="btn-check" name="account_type" id="asset" value="asset" autocomplete="off" checked>
                <label class="btn btn-outline-primary" for="asset">Asset</label>

                <input type="radio" class="btn-check" name="account_type" id="liability" value="liability" autocomplete="off">
                <label class="btn btn-outline-primary" for="liability">Liability</label>

                <input type="radio" class="btn-check" name="account_type" id="equity" value="equity" autocomplete="off">
                <label class="btn btn-outline-primary" for="equity">Equity</label>

                <input type="radio" class="btn-check" name="account_type" id="revenue" value="revenue" autocomplete="off">
                <label class="btn btn-outline-primary" for="revenue">Revenue</label>

                <input type="radio" class="btn-check" name="account_type" id="expense" value="expense" autocomplete="off">
                <label class="btn btn-outline-primary" for="expense">Expense</label>
            </div>
        </div>

        <div class="mb-3">
            <label for="balance" class="form-label">Balance</label>
            <input type="number" step="0.01" class="form-control" id="balance" name="balance" placeholder="Enter balance" required>
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

    {{-- Filter bar to search accounts by type using radio buttons --}}
    <div class="mt-5">
        <h3>Your Accounts</h3>
        <form method="GET" action="{{ route('create.acc.show') }}" class="mb-3">
            <label class="form-label d-block">Filter by Account Type</label>
            <div class="btn-group" role="group" aria-label="Account Type Filter">
                <input type="radio" class="btn-check" name="type" id="filter_all" value="all" autocomplete="off"
                       {{ request('type', 'all') == 'all' ? 'checked' : '' }}>
                <label class="btn btn-outline-secondary" for="filter_all">All</label>

                <input type="radio" class="btn-check" name="type" id="filter_asset" value="asset" autocomplete="off"
                       {{ request('type') == 'asset' ? 'checked' : '' }}>
                <label class="btn btn-outline-secondary" for="filter_asset">Asset</label>

                <input type="radio" class="btn-check" name="type" id="filter_liability" value="liability" autocomplete="off"
                       {{ request('type') == 'liability' ? 'checked' : '' }}>
                <label class="btn btn-outline-secondary" for="filter_liability">Liability</label>

                <input type="radio" class="btn-check" name="type" id="filter_equity" value="equity" autocomplete="off"
                       {{ request('type') == 'equity' ? 'checked' : '' }}>
                <label class="btn btn-outline-secondary" for="filter_equity">Equity</label>

                <input type="radio" class="btn-check" name="type" id="filter_revenue" value="revenue" autocomplete="off"
                       {{ request('type') == 'revenue' ? 'checked' : '' }}>
                <label class="btn btn-outline-secondary" for="filter_revenue">Revenue</label>

                <input type="radio" class="btn-check" name="type" id="filter_expense" value="expense" autocomplete="off"
                       {{ request('type') == 'expense' ? 'checked' : '' }}>
                <label class="btn btn-outline-secondary" for="filter_expense">Expense</label>
            </div>
            <button type="submit" class="btn btn-primary ms-2">Filter</button>
        </form>
    </div>

    {{-- Display account details in a table --}}
    @if(isset($accounts) && $accounts->count())
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Balance</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($accounts as $account)
                    <tr>
                        <td>{{ $account->name }}</td>
                        <td>{{ ucfirst($account->acc_type) }}</td>
                        <td>${{ number_format($account->balance, 2) }}</td>
                        <td>{{ $account->created_at->format('Y-m-d') }}</td>
                        <td>
                            {{-- Deactivate Account Form --}}
                            <form action="{{ route('accounts.destroy', $account) }}" method="POST" class="d-inline delete-account-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>

                            {{-- Add Balance Form --}}
                            <form action="{{ route('accounts.add-balance', $account) }}" method="POST" class="d-inline">
                                @csrf
                                <div class="input-group input-group-sm" style="max-width: 200px; margin-top: 5px;">
                                    <input type="number" step="0.01" name="amount" class="form-control" placeholder="Add Balance" required>
                                    <button class="btn btn-sm btn-success" type="submit">Add</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No accounts found. Create one above!</p>
    @endif
</div>
@endsection

@section('scripts')
<script>
// Wait until the DOM is fully loaded.
document.addEventListener('DOMContentLoaded', function () {
    // Select all forms with the class 'delete-account-form'
    const deleteForms = document.querySelectorAll('.delete-account-form');

    deleteForms.forEach(function(form) {
        form.addEventListener('submit', function(event) {
            // Display a confirmation dialog.
            if (!confirm('Are you sure you want to delete this account?')) {
                // Prevent form submission if the user cancels.
                event.preventDefault();
            }
        });
    });
});
</script>
@endsection
