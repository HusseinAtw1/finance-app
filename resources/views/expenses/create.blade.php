@extends('welcome')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1 class="mb-4 text-center">Create Expense</h1>
            <form action="{{ route('expenses.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Expense Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter expense name" value="{{ old('name') }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="reference_number" class="form-label">Reference Number</label>
                        <input type="text" class="form-control" id="reference_number" name="reference_number" placeholder="Enter Reference Number" value="{{ old('reference_number') }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="currency_id" class="form-label">Currency</label>
                        <select class="form-select" id="currency_id" name="currency_id">
                            <option value="">Select Currency</option>
                            @foreach($currencies as $currency)
                                <option value="{{ $currency->id }}" {{ old('currency_id') == $currency->id ? 'selected' : '' }}>
                                    {{ $currency->code ?? $currency->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="total_toBePaid" class="form-label">Total Amount To Be Paid</label>
                        <input type="number" step="0.01" class="form-control" id="total_toBePaid" name="total_toBePaid" placeholder="Total amount to be paid" value="{{ old('total_toBePaid') }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="due_date" class="form-label">Due Date</label>
                    <input type="date" class="form-control" id="due_date" name="due_date" value="{{ old('due_date') }}">
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Additional details...">{{ old('description') }}</textarea>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Create Expense</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
