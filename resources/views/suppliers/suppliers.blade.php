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
            <form action="{{ route('suppliers.store') }}" method="POST" id="supplierForm">
                @csrf
                <div id="methodContainer"></div>

                <div class="form-floating mb-3">
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="Supplier Name" value="{{ old('name') }}">
                    <label for="name">Supplier Name</label>
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-floating mb-3">
                    <input type="text" name="phone_number" id="phone_number" class="form-control @error('phone_number') is-invalid @enderror" placeholder="Phone Number" value="{{ old('phone_number') }}">
                    <label for="phone_number">Phone Number</label>
                    @error('phone_number')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary" id="submitButton">Create Supplier</button>
                <button type="button" class="btn btn-secondary" id="cancelButton" style="display: none;">Cancel</button>
            </form>
        </div>

        <div class="col-md-12 mt-4">
            <h2>All Suppliers</h2>

            @if($suppliers->isEmpty())
                <p>No suppliers found.</p>
            @else
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone Number</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($suppliers as $supplier)
                            <tr data-id="{{ $supplier->id }}"
                                data-name="{{ $supplier->name }}"
                                data-phone-number="{{ $supplier->phone_number }}">
                                <td>{{ $supplier->name }}</td>
                                <td>{{ $supplier->phone_number }}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm edit-btn">Edit</button>
                                    <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" style="display:inline;">
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
    const form = document.getElementById('supplierForm');
    const submitButton = document.getElementById('submitButton');
    const cancelButton = document.getElementById('cancelButton');

    // Check if we're coming back from a validation error in edit mode
    const currentUrl = window.location.href;
    const isEditMode = currentUrl.includes('/suppliers/') && currentUrl.includes('/edit');

    if (isEditMode) {
        // Extract supplier ID from URL (if editing)
        const urlParts = currentUrl.split('/');
        const supplierId = urlParts[urlParts.indexOf('suppliers') + 1];

        // Set up form for edit mode
        form.action = `/suppliers/${supplierId}`;
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PUT';
        document.getElementById('methodContainer').innerHTML = '';
        document.getElementById('methodContainer').appendChild(methodInput);

        submitButton.textContent = 'Update Supplier';
        cancelButton.style.display = 'inline-block';
    }

    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');

            document.getElementById('name').value = row.dataset.name;
            document.getElementById('phone_number').value = row.dataset.phoneNumber;

            form.action = `/suppliers/${row.dataset.id}`;
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PUT';
            document.getElementById('methodContainer').innerHTML = '';
            document.getElementById('methodContainer').appendChild(methodInput);

            submitButton.textContent = 'Update Supplier';
            cancelButton.style.display = 'inline-block';
        });
    });

    cancelButton.addEventListener('click', function() {
        form.reset();
        form.action = "{{ route('suppliers.store') }}";
        document.getElementById('methodContainer').innerHTML = '';
        submitButton.textContent = 'Create Supplier';
        cancelButton.style.display = 'none';
    });
});
</script>
@endsection
