@extends('welcome')

@section('content')
<div class="container mt-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
         <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
            </ul>
         </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <h2 id="form-title">Add New Asset Category</h2>
            <form id="assetCategoryForm" action="{{ route('asset_categories.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" value="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Category Name</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Enter category name" required>
                </div>
                <input type="hidden" id="assetCategoryId" name="assetCategoryId" value="">
                <button type="submit" id="submitButton" class="btn btn-primary">Add Category</button>
                <button type="button" id="cancelEditButton" class="btn btn-secondary" style="display: none;">Cancel Edit</button>
            </form>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-md-12">
            <h2>Asset Categories</h2>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assetCategories as $assetCategory)
                        <tr>
                            <td>{{ $assetCategory->name }}</td>
                            <td>{{ $assetCategory->created_at->format('Y-m-d') }}</td>
                            <td>
                                <button type="button"
                                    class="btn btn-success btn-sm edit-button"
                                    data-id="{{ $assetCategory->id }}"
                                    data-name="{{ $assetCategory->name }}">
                                    Edit
                                </button>
                                <form action="{{ route('asset_categories.destroy', $assetCategory->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No asset categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.edit-button').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');

            document.getElementById('name').value = name;
            document.getElementById('assetCategoryId').value = id;
            document.getElementById('form-title').innerText = 'Edit Asset Category';
            document.getElementById('submitButton').innerText = 'Update Category';

            const form = document.getElementById('assetCategoryForm');
            form.action = `/asset_categories/${id}`;

            let methodInput = document.querySelector('input[name="_method"]');
            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.setAttribute('type', 'hidden');
                methodInput.setAttribute('name', '_method');
                form.appendChild(methodInput);
            }
            methodInput.value = 'PUT';

            document.getElementById('cancelEditButton').style.display = 'inline-block';
        });
    });

    document.getElementById('cancelEditButton').addEventListener('click', function() {
        document.getElementById('name').value = '';
        document.getElementById('assetCategoryId').value = '';
        document.getElementById('form-title').innerText = 'Add New Asset Category';
        document.getElementById('submitButton').innerText = 'Add Category';

        const form = document.getElementById('assetCategoryForm');
        form.action = "{{ route('asset_categories.store') }}";

        let methodInput = document.querySelector('input[name="_method"]');
        if (methodInput) {
            methodInput.value = '';
        }

        this.style.display = 'none';
    });
</script>
@endsection
