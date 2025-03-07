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
                    <i class="fas fa-money-bill-wave me-2"></i>{{ $expense->name }}
                </h2>
                <div>
                    <span class="badge bg-light text-dark me-2 fs-6 py-2 px-3">
                        Reference Number: {{ $expense->reference_number }}
                    </span>
                    @if ($expense->status === 'pending')
                        <span class="badge bg-primary fs-6 py-2 px-3">{{ ucfirst($expense->status) }}</span>
                    @elseif ($expense->status === 'paid')
                        <span class="badge bg-success fs-6 py-2 px-3">{{ ucfirst($expense->status) }}</span>
                    @elseif ($expense->status === 'overdue')
                        <span class="badge bg-danger fs-6 py-2 px-3">{{ ucfirst($expense->status) }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="info-section">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-info-circle me-2"></i>Expense Information
                        </h5>
                        <dl class="row mb-0">
                            <dt class="col-sm-5">Currency</dt>
                            <dd class="col-sm-7">
                                {{ optional($expense->currency)->symbol }} {{ optional($expense->currency)->name }}
                                @if($expense->currency_exchange_rate)
                                    <small class="text-muted d-block">
                                        Rate: {{ $expense->currency_exchange_rate }}
                                    </small>
                                @endif
                            </dd>
                            <dt class="col-sm-5">Amount Paid</dt>
                            <dd class="col-sm-7">
                                {{ optional($expense->currency)->symbol }} {{ number_format($expense->paid_amount, 2) }}
                            </dd>
                            <dt class="col-sm-5">Total To be Paid</dt>
                            <dd class="col-sm-7">
                                {{ optional($expense->currency)->symbol }} {{ number_format($expense->total_toBePaid, 2) }}
                            </dd>
                            <dt class="col-sm-5">Due Date</dt>
                            <dd class="col-sm-7">
                                @if($expense->due_date)
                                    {{ \Carbon\Carbon::parse($expense->due_date)->format('M d, Y') }}
                                @else
                                    N/A
                                @endif
                            </dd>
                            @if($expense->paid_at)
                                <dt class="col-sm-5">Payment Date</dt>
                                <dd class="col-sm-7">
                                    {{ \Carbon\Carbon::parse($expense->paid_at)->format('M d, Y H:i') }}
                                </dd>
                            @endif
                        </dl>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="info-section">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-sticky-note me-2"></i>Additional Notes
                        </h5>
                        <div class="p-3 border rounded bg-white">
                            {{ $expense->description ?? 'No additional notes provided' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="border-top pt-3 text-muted small">
                        <div class="row">
                            <div class="col-md-6">
                                Created: {{ \Carbon\Carbon::parse($expense->created_at)->format('M d, Y H:i') }}
                            </div>
                            <div class="col-md-6 text-md-end">
                                Last Updated: {{ \Carbon\Carbon::parse($expense->updated_at)->format('M d, Y H:i') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
