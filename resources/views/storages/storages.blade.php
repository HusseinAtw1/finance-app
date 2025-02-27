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
        <!-- Form Section -->
        <div class="col-md-4">
            <form action="{{ route('storages.store') }}" method="POST" id="storageForm">
                @csrf
                <div id="methodContainer"></div>

                <div class="form-floating mb-3">
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="Storage Name" value="{{ old('name') }}">
                    <label for="name">Storage Name</label>
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-floating mb-3">
                    <input type="text" name="address" id="address" class="form-control @error('address') is-invalid @enderror" placeholder="Detailed Address" value="{{ old('address') }}">
                    <label for="address">Detailed Address (Optional)</label>
                    @error('address')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-floating mb-3">
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" placeholder="Extra details" style="height: 100px;">{{ old('description') }}</textarea>
                    <label for="description">Extra Details (Optional)</label>
                    @error('description')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary" id="submitButton">Create Storage</button>
                <button type="button" class="btn btn-secondary" id="cancelButton" style="display: none;">Cancel</button>
            </form>
        </div>

        <!-- Listing Section -->
        <div class="col-md-12 mt-4">
            <h2>All Storages</h2>

            @if($storages->isEmpty())
                <p>No storages found.</p>
            @else
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Storage Name</th>
                            <th>Address</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($storages as $storage)
                            <tr data-id="{{ $storage->id }}"
                                data-name="{{ $storage->name }}"
                                data-address="{{ $storage->address }}"
                                data-description="{{ $storage->description }}">
                                <td>{{ $storage->name }}</td>
                                <td>{{ $storage->address ?? '-' }}</td>
                                <td>{{ $storage->description ?? '-' }}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm edit-btn">Edit</button>
                                    <form action="{{ route('storages.destroy', $storage->id) }}" method="POST" style="display:inline;">
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
    const form = document.getElementById('storageForm');
    const submitButton = document.getElementById('submitButton');
    const cancelButton = document.getElementById('cancelButton');

    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            document.getElementById('name').value = row.dataset.name;
            document.getElementById('address').value = row.dataset.address;
            document.getElementById('description').value = row.dataset.description;

            form.action = `/storages/${row.dataset.id}`;
            let methodInput = form.querySelector('input[name="_method"]');
            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                form.appendChild(methodInput);
            }
            methodInput.value = 'PUT';

            submitButton.textContent = 'Update Storage';
            cancelButton.style.display = 'inline-block';
        });
    });

    cancelButton.addEventListener('click', function() {
        form.reset();
        form.action = "{{ route('storages.store') }}";
        const methodInput = form.querySelector('input[name="_method"]');
        if(methodInput) methodInput.remove();
        submitButton.textContent = 'Create Storage';
        cancelButton.style.display = 'none';
    });
});
</script>
@endsection
