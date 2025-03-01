@extends('welcome')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">Assets</h1>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form action="{{ route('assets.show') }}" method="GET">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status->id }}" {{ request('status') == $status->id ? 'selected' : '' }}>
                                    {{ $status->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <select name="category" class="form-select">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <select name="type" class="form-select">
                            <option value="">All Types</option>
                            @foreach($types as $assetType)
                                <option value="{{ $assetType->id }}" {{ request('type') == $assetType->id ? 'selected' : '' }}>
                                    {{ $assetType->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <select name="storage" class="form-select">
                            <option value="">All Storages</option>
                            @foreach($storages as $storage)
                                <option value="{{ $storage->id }}" {{ request('storage') == $storage->id ? 'selected' : '' }}>
                                    {{ $storage->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-1 col-md-6">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-funnel me-1"></i>Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        @forelse($assets as $asset)
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0 hover-shadow">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 text-truncate">{{ $asset->name }}</h5>
                        <span class="badge bg-primary rounded-pill">ID: {{ $asset->id }}</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Status</span>
                                <span class="fw-bold">{{ $asset->assetStatus->name ?? 'N/A' }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Category</span>
                                <span class="fw-bold">{{ $asset->assetCategory->name ?? 'N/A' }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Type</span>
                                <span class="fw-bold">{{ $asset->assetType->name ?? 'N/A' }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Storage</span>
                                <span class="fw-bold">{{ $asset->storage->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 p-3">
                        <a href=" {{ route('assets_detials.show', $asset->id)}} " class="btn btn-outline-primary w-100">
                            <i class="bi bi-eye me-2"></i>View Details
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info d-flex align-items-center shadow-sm">
                    <i class="bi bi-info-circle-fill me-2 fs-4"></i>
                    <div>No assets found. Try adjusting your search filters.</div>
                </div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $assets->links() }}
    </div>
</div>

<style>
    body {
        background-color:#e8e8e8;
    }
</style>
@endsection
