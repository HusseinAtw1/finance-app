@extends('welcome')

@section('content')
<div class="container mt-4">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-4">
            <form action="{{ route('currencies.store') }}" method="POST" id="currencyForm">
                @csrf
                <div id="methodContainer"></div>

                <div class="form-floating mb-3">
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="ISO 4217 Code" value="{{ old('name') }}">
                    <label for="name">ISO 4217 Code</label>
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-floating mb-3">
                    <input type="text" name="full_name" id="full_name" class="form-control @error('full_name') is-invalid @enderror" placeholder="Full Currency Name" value="{{ old('full_name') }}">
                    <label for="full_name">Full Currency Name</label>
                    @error('full_name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-floating mb-3">
                    <input type="text" name="symbol" id="symbol" class="form-control @error('symbol') is-invalid @enderror" placeholder="Currency Symbol" value="{{ old('symbol') }}">
                    <label for="symbol">Currency Symbol</label>
                    @error('symbol')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-floating mb-3">
                    <input type="text" name="exchange_rate" id="exchange_rate" class="form-control @error('exchange_rate') is-invalid @enderror" placeholder="Currency rate vs USD" value="{{ old('exchange_rate') }}">
                    <label for="exchange_rate">Currency Exchange Rate vs USD</label>
                    @error('exchange_rate')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary" id="submitButton">Create Currency</button>
                <button type="button" class="btn btn-secondary" id="cancelButton" style="display: none;">Cancel</button>
            </form>
        </div>

        <div class="col-md-12 mt-4">
            <h2>All Currencies</h2>

            @if($currencies->isEmpty())
                <p>No currencies found.</p>
            @else
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ISO 4217 Code</th>
                            <th>Full Currency Name</th>
                            <th>Symbol</th>
                            <th>Exchange Rate</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($currencies as $currency)
                            <tr data-id="{{ $currency->id }}"
                                data-name="{{ $currency->name }}"
                                data-full-name="{{ $currency->full_name }}"
                                data-symbol="{{ $currency->symbol }}"
                                data-exchange-rate="{{ $currency->exchange_rate }}">
                                <td>{{ $currency->name }}</td>
                                <td>{{ $currency->full_name }}</td>
                                <td>{{ $currency->symbol ?? '-' }}</td>
                                <td>{{ $currency->exchange_rate }}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm edit-btn">Edit</button>
                                    <form action="{{ route('currencies.destroy', $currency->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('currencyForm');
    const submitButton = document.getElementById('submitButton');
    const cancelButton = document.getElementById('cancelButton');
    let isEditMode = false;

    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            isEditMode = true;

            document.getElementById('name').value = row.dataset.name;
            document.getElementById('full_name').value = row.dataset.fullName;
            document.getElementById('symbol').value = row.dataset.symbol;
            document.getElementById('exchange_rate').value = row.dataset.exchangeRate;

            form.action = `/currencies/${row.dataset.id}`;
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PUT';
            form.appendChild(methodInput);

            submitButton.textContent = 'Update Currency';
            cancelButton.style.display = 'inline-block';
        });
    });

    cancelButton.addEventListener('click', function() {
        form.reset();
        form.action = "{{ route('currencies.store') }}";
        const methodInput = form.querySelector('input[name="_method"]');
        if(methodInput) methodInput.remove();
        submitButton.textContent = 'Create Currency';
        cancelButton.style.display = 'none';
        isEditMode = false;
    });
});
</script>
@endsection
