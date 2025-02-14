@props(['asset'])

<style>
    .card {
        border-radius: 15px;
    }
    .card-header {
        border-radius: 15px 15px 0 0 !important;
    }
    dt {
        font-weight: 500;
        color: #6c757d;
    }
    dd {
        color: #333;
    }
    .form-label {
        font-weight: 500;
    }
    .input-group-text {
        background-color: #e9ecef;
    }
</style>

<div class="card shadow-lg mb-5">
    <div class="card-header bg-primary text-white py-3">
        <h2 class="h4 mb-0"><i class="fas fa-cube me-2"></i>Asset Details - {{ $asset->name }}</h2>
    </div>
    <div class="card-body">
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="row g-3">
                    <div class="col-12">
                        <h5 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Basic Information</h5>
                        <dl class="row mb-0">
                            <dt class="col-sm-4">Account</dt>
                            <dd class="col-sm-8">{{ $asset->account->name }}</dd>

                            <dt class="col-sm-4">Currency</dt>
                            <dd class="col-sm-8">{{ optional($asset->currency)->symbol }} ({{ optional($asset->currency)->name }})</dd>

                            <dt class="col-sm-4">Asset Type</dt>
                            <dd class="col-sm-8">{{ $asset->assetType->name }}</dd>

                            <dt class="col-sm-4">Category</dt>
                            <dd class="col-sm-8">{{ $asset->assetCategory->name }}</dd>

                            <dt class="col-sm-4">Status</dt>
                            @if (in_array($asset->assetStatus->name, ['Active', 'Pending']))
                                <dd class="col-sm-8">
                                    <span class="badge bg-success">{{ $asset->assetStatus->name }}</span>
                                </dd>
                            @elseif (in_array($asset->assetStatus->name, ['Inactive', 'Archived', 'Suspended']))
                                <dd class="col-sm-8">
                                    <span class="badge bg-warning">{{ $asset->assetStatus->name }}</span>
                                </dd>
                            @else
                                <dd class="col-sm-8">
                                    <span class="badge bg-danger">{{ $asset->assetStatus->name }}</span>
                                </dd>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="row g-3">
                    <div class="col-12">
                        <h5 class="text-primary mb-3"><i class="fas fa-chart-line me-2"></i>Financial Details</h5>
                        <dl class="row mb-0">
                            <dt class="col-sm-4">Purchase Date</dt>
                            <dd class="col-sm-8">{{ $asset->purchase_at }} UTC</dd>

                            <dt class="col-sm-4">Quantity</dt>
                            <dd class="col-sm-8">{{ $asset->quantity }}</dd>

                            <dt class="col-sm-4">Current Value</dt>
                            <dd class="col-sm-8 text-success fw-bold">{{ $asset->current_value }}</dd>

                            <dt class="col-sm-4">Purchase Price</dt>
                            <dd class="col-sm-8">{{ $asset->purchase_price }}</dd>

                            <dt class="col-sm-4">Location</dt>
                            <dd class="col-sm-8">{{ $asset->location }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-top pt-4">
            <h5 class="text-primary mb-3"><i class="fas fa-sticky-note me-2"></i>Additional Notes</h5>
            <div class="bg-light p-3 rounded">
                {{ $asset->notes ?? 'No additional notes provided' }}
            </div>
        </div>
    </div>
</div>
