@extends('welcome')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Customers</h1>
        <div>
            <a href="{{ route('customers.index', ['export' => 'csv'] + request()->all()) }}" class="btn btn-secondary">
                <i class="fa fa-download"></i> Export CSV
            </a>
        </div>
    </div>

    <!-- Search Form -->
    <div class="row mb-4">
        <div class="col-md-6">
            <form action="{{ route('customers.index') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search by name or phone..." value="{{ $search ?? '' }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">Search</button>
                        @if(isset($search) && $search)
                            <a href="{{ route('customers.index') }}" class="btn btn-secondary">Clear</a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Customers Table -->
    <div class="card">
        <div class="card-body">
            @if($customers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>
                                    <a href="{{ route('customers.index', ['sort' => 'id', 'direction' => $sort == 'id' && $direction == 'asc' ? 'desc' : 'asc'] + request()->except(['sort', 'direction'])) }}">
                                        ID
                                        @if($sort == 'id')
                                            <i class="fa fa-chevron-{{ $direction == 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('customers.index', ['sort' => 'name', 'direction' => $sort == 'name' && $direction == 'asc' ? 'desc' : 'asc'] + request()->except(['sort', 'direction'])) }}">
                                        Name
                                        @if($sort == 'name')
                                            <i class="fa fa-chevron-{{ $direction == 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('customers.index', ['sort' => 'phone_number', 'direction' => $sort == 'phone_number' && $direction == 'asc' ? 'desc' : 'asc'] + request()->except(['sort', 'direction'])) }}">
                                        Phone
                                        @if($sort == 'phone_number')
                                            <i class="fa fa-chevron-{{ $direction == 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('customers.index', ['sort' => 'created_at', 'direction' => $sort == 'created_at' && $direction == 'asc' ? 'desc' : 'asc'] + request()->except(['sort', 'direction'])) }}">
                                        Created
                                        @if($sort == 'created_at')
                                            <i class="fa fa-chevron-{{ $direction == 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $customer)
                                <tr>
                                    <td>{{ $customer->id }}</td>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->phone_number }}</td>
                                    <td>{{ $customer->created_at->format('M d, Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $customers->appends(request()->except('page'))->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    No customers found.
                    @if(isset($search) && $search)
                        <a href="{{ route('customers.index') }}" class="alert-link">Clear search</a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function confirmDelete(customerId) {
        document.getElementById('deleteForm').action = "{{ route('customers.destroy', '') }}/" + customerId;
        $('#deleteModal').modal('show');
    }
</script>
@endsection
