@extends('welcome')

@section('content')
<div class="container-md mx-auto" style="max-width: 800px;">
    <h2 class="mb-4 text-center">Asset Statuses</h2>

    <form id="assetStatusForm" action="{{ route('asset_statuses.store') }}" method="POST" class="mb-4">
        @csrf
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            <span id="formTitle">Enter the name for your new asset status.</span>
        </div>

        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="name" name="name" placeholder="Asset Status Name" required>
            <label for="name">Asset Status Name</label>
        </div>

        <div class="d-grid gap-2 mx-auto" style="max-width: 200px;">
            <button type="submit" class="btn btn-primary btn-sm py-2 fw-bold" id="formButton">
                <i class="bi bi-plus-circle me-2"></i><span id="formButtonText">Add Asset Status</span>
            </button>
            <button type="button" class="btn btn-secondary btn-sm py-2 fw-bold mt-2" id="cancelButton" style="display: none;">
                <i class="bi bi-x-circle me-2"></i><span>Cancel Edit</span>
            </button>
        </div>
    </form>

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

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($statuses as $status)
                    <tr>
                        <td>{{ $status->id }}</td>
                        <td>{{ $status->name }}</td>
                        <td>{{ $status->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <button class="btn btn-sm btn-success edit-button" data-id="{{ $status->id }}" data-name="{{ $status->name }}" data-url="{{ route('asset_statuses.update', $status->id) }}">
                                <i class="bi bi-pencil-square"></i> Edit
                            </button>
                            <form action="{{ route('asset_statuses.destroy', $status->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this asset status?');">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editButtons = document.querySelectorAll('.edit-button');
        const form = document.getElementById('assetStatusForm');
        const nameInput = document.getElementById('name');
        const formTitle = document.getElementById('formTitle');
        const formButton = document.getElementById('formButton');
        const formButtonText = document.getElementById('formButtonText');
        const cancelButton = document.getElementById('cancelButton'); // <-- Added this line

        editButtons.forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const url = this.getAttribute('data-url');

                form.action = url;
                let methodInput = form.querySelector('input[name="_method"]');
                if (!methodInput) {
                    methodInput = document.createElement('input');
                    methodInput.setAttribute('type', 'hidden');
                    methodInput.setAttribute('name', '_method');
                    form.appendChild(methodInput);
                }
                methodInput.value = 'PUT';
                nameInput.value = name;
                formTitle.textContent = 'Edit the name of your asset status.';
                formButtonText.textContent = 'Update Asset Status';
                formButton.querySelector('i').className = 'bi bi-pencil-square me-2';
                form.scrollIntoView({ behavior: 'smooth' });

                // Show the cancel button when editing
                cancelButton.style.display = 'block';
            });
        });

        cancelButton.addEventListener('click', function () {
            // Reset form action back to the store route
            form.action = "{{ route('asset_statuses.store') }}";

            // Reset the hidden _method input (if it exists)
            let methodInput = form.querySelector('input[name="_method"]');
            if (methodInput) {
                methodInput.value = '';
            }

            // Clear the name input
            nameInput.value = '';
            formTitle.textContent = 'Enter the name for your new asset status.';
            formButtonText.textContent = 'Add Asset Status';
            formButton.querySelector('i').className = 'bi bi-plus-circle me-2';
            cancelButton.style.display = 'none';
        });
    });
</script>
@endsection
