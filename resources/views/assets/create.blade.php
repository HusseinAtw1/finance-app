@extends('welcome')

@section('content')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('assets.store') }}" method="POST" class="mt-4">
    <div class="container-md mx-auto" style="max-width: 800px;">
        <h2 class="mb-4 text-center">Add New Asset</h2>
        @csrf

        <div class="row g-3">
            <div class="col-12">
                <div class="form-floating">
                    <input type="text" class="form-control" id="name" name="name" placeholder="Asset Name" required>
                    <label for="name">Asset Name</label>
                </div>
            </div>

            <div class="col-12">
                <div class="form-floating">
                    <input type="text" class="form-control" id="reference_number" name="reference_number" placeholder="reference_number" required>
                    <label for="reference_number">Reference Number</label>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-floating">
                    <select class="form-select" id="type" name="type" required>
                        <option value="">Choose a type</option>
                        @foreach ($assetTypes as $type)
                            <option value="{{$type->id}}">{{$type->name}}</option>
                        @endforeach
                    </select>
                    <label for="type">Asset Type</label>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-floating">
                    <select class="form-select" id="category" name="category" required>
                        <option value="">Choose a category</option>
                        @foreach($assetCategories as $cat)
                        <option value="{{$cat->id}}">{{$cat->name}}</option>
                        @endforeach
                    </select>
                    <label for="category">Asset Category</label>
                </div>
            </div>

            <div class="col-12">
                <div class="form-floating">
                    <textarea class="form-control" id="notes" name="notes" style="height: 100px" placeholder="" required></textarea>
                    <label for="notes">Additional Notes</label>
                </div>
            </div>

            <div class="col-12 mt-4 mb-5">
                <div class="d-grid gap-2 col-md-6 mx-auto">
                    <button type="submit" class="btn btn-primary btn-lg py-2 fw-bold">
                        <i class="bi bi-plus-circle me-2"></i>Add Asset
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection
