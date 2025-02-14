@extends('welcome')

@section('content')
<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Your Assets</h2>
        <a href="{{ route('assets.create') }}" class="btn btn-primary">Add an Asset</a>
    </div>

    <form method="GET" action="{{ route('assets.show') }}">
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="account_id" class="form-label">Select Account:</label>
                <select name="account_id" id="account_id" class="form-control" onchange="this.form.submit()">
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}" {{ $selectedAccount == $account->id ? 'selected' : '' }}>
                            {{ $account->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label for="status" class="form-label">Asset Filter:</label>
                <select name="status" id="status" class="form-control" onchange="this.form.submit()">
                    <option value="owned" {{ request('status', 'owned') == 'owned' ? 'selected' : '' }}>Owned</option>
                    <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>Sold</option>
                </select>
            </div>

            <div class="col-md-4">
                <label for="search" class="form-label">Search Assets:</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Enter search term" value="{{ request('search') }}">
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-secondary w-100">Search</button>
            </div>
        </div>
    </form>

    <div class="row">
        @forelse ($assets as $asset)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center">
                        <span class="me-auto">{{ $asset->name }}</span>
                        <div class="d-flex gap-2">
                            <a href="{{ route('asset_update.show', $asset->id )}}" class="btn btn-success btn-sm">Update</a>
                            @if ($asset->assetStatus->name === 'Sold')
                                <a href="{{ route('assets_detials.show', $asset->id) }}" class="btn btn-primary btn-sm">View</a>
                            @else
                                <a href="{{ route('assets_detials.show', $asset->id) }}" class="btn btn-danger btn-sm">Sell</a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><strong>Purchased Price:</strong> {{ $asset->purchase_price }} {{$asset->currency->symbol}}</p>
                        <p class="card-text"><strong>Current Value:</strong> {{ $asset->current_value }} {{$asset->currency->symbol}}</p>
                        <p class="card-text"><strong>Quantity left:</strong> {{ $asset->quantity }}</p>
                        <p class="card-text"><strong>Date:</strong> {{ $asset->created_at->format('d M, Y') }}</p>
                        @if ($asset->assetStatus->name === 'Sold')
                            <p class="card-text"><strong>Status:</strong> <span class="badge text-bg-danger">Sold</span></p>
                        @elseif (in_array($asset->assetStatus->name, ['Inactive', 'Archived', 'Suspended']))
                            <p class="card-text"><strong>Status:</strong> <span class="badge text-bg-warning">{{$asset->assetStatus->name}}</span></p>
                        @else
                            <p class="card-text"><strong>Status:</strong> <span class="badge text-bg-success">{{$asset->assetStatus->name}}</span></p>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p>No assets found with the selected filters.</p>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $assets->appends(request()->query())->links() }}
    </div>
</div>
@endsection

