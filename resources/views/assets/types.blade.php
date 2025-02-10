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
            <form id="assetTypeForm" action="{{ route('asset_types.store') }}" method="POST">
                @csrf
                @method('POST')

                <input type="hidden" id="asset_type_id" name="asset_type_id">
                <div class="form-floating mb-3">
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="Asset Type Name" value="{{ old('name') }}">
                    <label for="name">Asset Type Name</label>
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" id="submitButton" class="btn btn-primary">Add Asset Type</button>
                <button type="button" id="cancelButton" class="btn btn-secondary d-none">Cancel</button>
            </form>
        </div>
        <div class="col-md-12 mt-4">
            <h2>All Asset Types</h2>

            @if($assetTypes->isEmpty())
                <p>No asset types found.</p>
            @else
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Asset Type Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assetTypes as $assetType)
                            <tr>
                                <td>{{ $assetType->name }}</td>
                                <td class="d-flex gap-2">
                                    <button type="button" class="btn btn-success btn-sm edit-btn"
                                        data-id="{{ $assetType->id }}"
                                        data-name="{{ $assetType->name }}">
                                        Edit
                                    </button>
                                    <form action="{{ route('asset_types.destroy', $assetType->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this asset type?');">
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

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById("assetTypeForm");
        const assetTypeIdInput = document.getElementById("asset_type_id");
        const nameInput = document.getElementById("name");
        const submitButton = document.getElementById("submitButton");
        const cancelButton = document.getElementById("cancelButton");
        const editButtons = document.querySelectorAll(".edit-btn");
        editButtons.forEach(button => {
            button.addEventListener("click", function() {
                const assetTypeId = this.getAttribute("data-id");
                const assetTypeName = this.getAttribute("data-name");
                assetTypeIdInput.value = assetTypeId;
                nameInput.value = assetTypeName;
                form.action = `{{ url('asset_types') }}/${assetTypeId}`;
                form.querySelector("input[name='_method']").value = "PUT";
                submitButton.textContent = "Update Asset Type";
                cancelButton.classList.remove("d-none");
            });
        });
        cancelButton.addEventListener("click", function() {
            assetTypeIdInput.value = "";
            nameInput.value = "";
            form.action = "{{ route('asset_types.store') }}";
            form.querySelector("input[name='_method']").value = "POST";
            submitButton.textContent = "Add Asset Type";
            submitButton.classList.remove("btn-warning");
            submitButton.classList.add("btn-primary");
            cancelButton.classList.add("d-none");
        });
    });
</script>
@endsection
