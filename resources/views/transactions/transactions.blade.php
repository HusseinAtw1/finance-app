@extends('welcome')

@section('content')
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center w-100">
        <h1>Transactions</h1>
        <form action="{{ route('create_transaction.create') }}" method="POST">
            @csrf
            <input type="submit" value="Create Transaction" class="btn btn-primary">
        </form>
    </div>

    <!-- Table displaying transactions -->
    <div class="mt-4">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Transaction Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @if (!$transactions)
                    <tr>
                        <td colspan="4" class="text-center">No transactions found.</td>
                    </tr>
                @else
                    @foreach($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->status }}</td>
                            <td>{{ $transaction->total }}</td>
                            <td>{{ $transaction->transaction_date }}</td>
                            <td>
                                <a href="{{ route('transaction_create.show', $transaction->id) }}" class="btn btn-info">
                                    View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
