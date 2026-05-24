@extends('layouts.app')

@section('title', 'Reports - FitLife ERP')
@section('page-title', 'Business Intelligence Reports')

@section('content')
@if (isset($error))
    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $error }}
    </div>
@else
    <div class="row g-4 mb-4">
        <!-- Revenue Report Card -->
        <div class="col-md-8">
            <div class="card card-premium p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-wallet2 me-2 text-indigo"></i>Revenue Dashboard</h5>
                    <span class="badge bg-success rounded-pill px-3 py-1">Live Financial Data</span>
                </div>
                <div class="row g-4 align-items-center">
                    <div class="col-md-5 text-center text-md-start">
                        <div class="text-muted small fw-bold text-uppercase">Total Monthly Revenue</div>
                        <h1 class="display-4 fw-bold text-dark mt-1 mb-2">${{ number_format($reportData['total_revenue_this_month'], 2) }}</h1>
                        <p class="text-success small mb-0"><i class="bi bi-graph-up-arrow me-1"></i>From subscriptions and renewals</p>
                    </div>
                    <div class="col-md-7">
                        <div class="p-3 bg-light rounded-4">
                            <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-2">
                                <span class="text-muted small">Subscription Plan Enrollments</span>
                                <span class="fw-bold text-dark">{{ $reportData['new_members_this_month'] }}</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-2">
                                <span class="text-muted small">Membership Renewals</span>
                                <span class="fw-bold text-dark">{{ $reportData['renewals_this_month'] }}</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="text-muted small">Average Paid per Subscription</span>
                                @php
                                    $denominator = ($reportData['new_members_this_month'] + $reportData['renewals_this_month']);
                                    $average = $denominator > 0 ? $reportData['total_revenue_this_month'] / $denominator : 0;
                                @endphp
                                <span class="fw-bold text-indigo">${{ number_format($average, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Membership Insights Card -->
        <div class="col-md-4">
            <div class="card card-premium p-4 h-100">
                <h5 class="fw-bold mb-4"><i class="bi bi-pie-chart-fill me-2 text-indigo"></i>Membership Insights</h5>
                <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                    <div>
                        <div class="fw-bold text-dark">Active Subscribers</div>
                        <small class="text-muted">Total currently active members</small>
                    </div>
                    <h3 class="mb-0 fw-bold text-primary">{{ $reportData['total_active_members'] }}</h3>
                </div>
                <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                    <div>
                        <div class="fw-bold text-dark">New Members (Month)</div>
                        <small class="text-muted">Registered in the current month</small>
                    </div>
                    <h3 class="mb-0 fw-bold text-success">{{ $reportData['new_members_this_month'] }}</h3>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="fw-bold text-dark">Expiring (7 Days)</div>
                        <small class="text-muted">Subscriptions expiring soon</small>
                    </div>
                    <h3 class="mb-0 fw-bold text-warning">{{ $reportData['expiring_this_week'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert / Actions Row -->
    <div class="row">
        <div class="col-12">
            <div class="card card-premium p-4 bg-light">
                <h5 class="fw-bold mb-3">System Report Verification Status</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="p-3 bg-white rounded-3 shadow-sm d-flex align-items-center gap-3">
                            <i class="bi bi-database-check text-success fs-3"></i>
                            <div>
                                <div class="fw-bold text-dark">CRM Service</div>
                                <small class="text-muted">Status: Active</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-white rounded-3 shadow-sm d-flex align-items-center gap-3">
                            <i class="bi bi-database-check text-success fs-3"></i>
                            <div>
                                <div class="fw-bold text-dark">Membership Service</div>
                                <small class="text-muted">Status: Active</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-white rounded-3 shadow-sm d-flex align-items-center gap-3">
                            <i class="bi bi-database-check text-success fs-3"></i>
                            <div>
                                <div class="fw-bold text-dark">Reporting Service</div>
                                <small class="text-muted">Status: Active</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
