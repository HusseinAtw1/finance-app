@extends('welcome')

@section('content')
<div class="container mt-4">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <!-- Form on the Left -->
        <div class="col-md-4">
            <form action="{{ route('currencies.store') }}" method="POST">
                @csrf

                <!-- ISO 4217 Code -->
                <div class="form-floating mb-3">
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="ISO 4217 Code" value="{{ old('name') }}">
                    <label for="name">ISO 4217 Code</label>
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Full Currency Name -->
                <div class="form-floating mb-3">
                    <input type="text" name="full_name" id="full_name" class="form-control @error('full_name') is-invalid @enderror" placeholder="Full Currency Name" value="{{ old('full_name') }}">
                    <label for="full_name">Full Currency Name</label>
                    @error('full_name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Currency Symbol (optional) -->
                <div class="form-floating mb-3">
                    <input type="text" name="symbol" id="symbol" class="form-control @error('symbol') is-invalid @enderror" placeholder="Currency Symbol" value="{{ old('symbol') }}">
                    <label for="symbol">Currency Symbol</label>
                    @error('symbol')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Create Currency</button>
            </form>
        </div>

        <!-- Currencies Table Below -->
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
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($currencies as $currency)
                            <tr>
                                <td>{{ $currency->name }}</td>
                                <td>{{ $currency->full_name }}</td>
                                <td>{{ $currency->symbol ?? '-' }}</td>
                                <td>
                                    <form action="{{ route('currencies.destroy', $currency->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this currency?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
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
@endsection
