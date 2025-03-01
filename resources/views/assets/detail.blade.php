@extends('welcome')

@section('content')

<div class="container my-5">
    <style>
        .card {
            border-radius: 15px;
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
        }
        .card-header {
            border-radius: 15px 15px 0 0 !important;
            background-color: #4a6cf7 !important;
        }
        dt {
            font-weight: 500;
            color: #6c757d;
        }
        dd {
            color: #333;
            margin-bottom: 0.5rem;
        }
        .value-history {
            max-height: 300px;
            overflow-y: auto;
        }
        .info-section {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            height: 100%;
        }
    </style>

    <div class="card mb-5">
        <div class="card-header py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="h4 mb-0 text-white">
                    <i class="fas fa-cube me-2"></i>{{ $asset->name }}
                </h2>
                <div>
                    <span class="badge bg-light text-dark me-2 fs-6 py-2 px-3">Reference Number: {{ $asset->reference_number }}</span>
                    @if (in_array($asset->assetStatus->name, ['Active', 'Pending']))
                        <span class="badge bg-success fs-6 py-2 px-3">{{ $asset->assetStatus->name }}</span>
                    @elseif (in_array($asset->assetStatus->name, ['Inactive', 'Archived', 'Suspended']))
                        <span class="badge bg-warning text-dark fs-6 py-2 px-3">{{ $asset->assetStatus->name }}</span>
                    @else
                        <span class="badge bg-danger fs-6 py-2 px-3">{{ $asset->assetStatus->name }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-section">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-info-circle me-2"></i>Asset Information
                                </h5>
                                <dl class="row mb-0">
                                    <dt class="col-sm-5">Type</dt>
                                    <dd class="col-sm-7">{{ $asset->assetType->name }}</dd>

                                    <dt class="col-sm-5">Category</dt>
                                    <dd class="col-sm-7">{{ $asset->assetCategory->name }}</dd>

                                    <dt class="col-sm-5">Storage Location</dt>
                                    <dd class="col-sm-7">{{ $asset->storage->name }}</dd>

                                    <dt class="col-sm-5">Quantity</dt>
                                    <dd class="col-sm-7">{{ $asset->quantity ?? 'N/A' }}</dd>

                                    <dt class="col-sm-5">Depreciation Method</dt>
                                    <dd class="col-sm-7">{{ $asset->assetDepreciation->method }}</dd>
                                </dl>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-section">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-chart-line me-2"></i>Financial Details
                                </h5>
                                <dl class="row mb-0">
                                    <dt class="col-sm-5">Currency</dt>
                                    <dd class="col-sm-7">
                                        {{ optional($asset->currency)->symbol }} {{ optional($asset->currency)->name }}
                                        @if($asset->currency_exchange_rate)
                                            <small class="text-muted d-block">Rate: {{ $asset->currency_exchange_rate }}</small>
                                        @endif
                                    </dd>

                                    <dt class="col-sm-5">Current Value</dt>
                                    <dd class="col-sm-7 text-success fw-bold">
                                        {{ optional($asset->currency)->symbol }} {{ number_format($asset->current_value, 2) }}
                                    </dd>

                                    <dt class="col-sm-5">Purchase Price</dt>
                                    <dd class="col-sm-7">
                                        {{ optional($asset->currency)->symbol }} {{ number_format($asset->purchase_price, 2) }}
                                    </dd>

                                    <dt class="col-sm-5">Purchase Date</dt>
                                    <dd class="col-sm-7">
                                        @if($asset->purchase_at)
                                            {{ \Carbon\Carbon::parse($asset->purchase_at)->format('M d, Y') }}
                                        @else
                                            N/A
                                        @endif
                                    </dd>
                                </dl>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="info-section">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-sticky-note me-2"></i>Additional Notes
                                </h5>
                                <div class="p-3 border rounded bg-white">
                                    {{ $asset->notes ?? 'No additional notes provided' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="info-section">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-history me-2"></i>Value History
                        </h5>
                        <div class="value-history">
                            @if($asset->valueHistory && $asset->valueHistory->count() > 0)
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th class="text-end">Value</th>
                                            <th class="text-end">Change</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $prevValue = null;
                                        @endphp
                                        @foreach($asset->valueHistory->sortByDesc('recorded_at') as $history)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($history->recorded_at)->format('M d, Y') }}</td>
                                                <td class="text-end">{{ optional($asset->currency)->symbol }} {{ number_format($history->value, 2) }}</td>
                                                <td class="text-end">
                                                    @if($prevValue !== null)
                                                        @php
                                                            $change = $history->value - $prevValue;
                                                            $changePercent = $prevValue > 0 ? ($change / $prevValue) * 100 : 0;
                                                        @endphp
                                                        <span class="{{ $change >= 0 ? 'text-success' : 'text-danger' }}">
                                                            {{ $change >= 0 ? '+' : '' }}{{ number_format($changePercent, 2) }}%
                                                        </span>
                                                    @endif
                                                    @php
                                                        $prevValue = $history->value;
                                                    @endphp
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="alert alert-info">No value history available</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="border-top pt-3 text-muted small">
                        <div class="row">
                            <div class="col-md-6">
                                Created: {{ \Carbon\Carbon::parse($asset->created_at)->format('M d, Y H:i') }}
                            </div>
                            <div class="col-md-6 text-md-end">
                                Last Updated: {{ \Carbon\Carbon::parse($asset->updated_at)->format('M d, Y H:i') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
