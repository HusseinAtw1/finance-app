@extends('welcome')

@section('content')
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center w-100">
        <h1>Transactions</h1>
        <a href="{{ route('create_transaction.show')}}" class="btn btn-primary">Create Transaction</a>
    </div>

    <form method="GET" action="{{ route('transactions.show') }}" class="mb-4">
        <div class="row">
            <div class="col-md-3">
                <label for="acc_type" class="form-label">Account Type:</label>
                <select name="acc_type" id="acc_type" class="form-select">
                    <option value="">All</option>
                    <option value="asset">Asset</option>
                    <option value="liability">Liability</option>
                    <option value="equity">Equity</option>
                    <option value="revenue">Revenue</option>
                    <option value="expense">Expense</option>
                </select>
            </div>

            <div class="col-md-3">
                <label for="trans_type" class="form-label">Transaction Type:</label>
                <select name="trans_type" id="trans_type" class="form-select">
                    <option value="">All</option>
                    <option value="credit">Credit</option>
                    <option value="debit">Debit</option>
                </select>
            </div>

            <div class="col-md-3">
                <label for="search" class="form-label">Search Description:</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Search...">
            </div>

            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="card-body">
            @if($transs->count())
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Account</th>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transs as $transaction)
                            <tr>
                                <td>{{ $transaction->id }}</td>
                                <td>{{ $transaction->account->name }}</td>
                                <td>{{ number_format($transaction->amount, 2) }}</td>
                                <td>{{ ucfirst($transaction->type) }}</td>
                                <td>{{ $transaction->description }}</td>
                                <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $transs->appends(request()->query())->links() }}
            @else
                <p>No transactions found.</p>
            @endif
        </div>
    </div>
</div>
@endsection
