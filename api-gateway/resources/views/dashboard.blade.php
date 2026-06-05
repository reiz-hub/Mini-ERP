@extends('layouts.app')

@section('title', 'Dashboard - FitLife ERP')
@section('page-title', 'Dashboard Overview')

@section('content')
<div class="row g-4 mb-4">
    <!-- Stat 1: Total Active Members -->
    <div class="col-md-3">
        <div class="card card-premium p-4 border-start border-primary border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem;">Active Members</h6>
                    <h3 class="mb-0 fw-bold">{{ $stats['total_active_members'] }}</h3>
                </div>
                <div class="bg-primary bg-opacity-10 p-3 rounded-3 text-primary">
                    <i class="bi bi-people-fill fs-4"></i>
                </div>
            </div>
            <div class="mt-3 text-muted small">
                <span class="text-success fw-bold"><i class="bi bi-plus-lg me-1"></i>{{ $stats['new_members_this_month'] }}</span> this month
            </div>
        </div>
    </div>

    <!-- Stat 2: Revenue This Month -->
    <div class="col-md-3">
        <div class="card card-premium p-4 border-start border-success border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem;">Revenue (Month)</h6>
                    <h3 class="mb-0 fw-bold">₱{{ number_format($stats['total_revenue_this_month'], 2) }}</h3>
                </div>
                <div class="bg-success bg-opacity-10 p-3 rounded-3 text-success">
                    <i class="bi bi-currency-dollar fs-4"></i>
                </div>
            </div>
            <div class="mt-3 text-muted small">
                <span class="text-primary fw-bold">{{ $stats['renewals_this_month'] }}</span> renewals this month
            </div>
        </div>
    </div>

    <!-- Stat 3: Expiring Memberships -->
    <div class="col-md-3">
        <div class="card card-premium p-4 border-start border-warning border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem;">Expiring Soon</h6>
                    <h3 class="mb-0 fw-bold">{{ $stats['expiring_this_week'] }}</h3>
                </div>
                <div class="bg-warning bg-opacity-10 p-3 rounded-3 text-warning">
                    <i class="bi bi-exclamation-octagon-fill fs-4"></i>
                </div>
            </div>
            <div class="mt-3 text-muted small">
                Expiring within the next 7 days
            </div>
        </div>
    </div>

    <!-- Stat 4: Staff & Employees -->
    <div class="col-md-3">
        <div class="card card-premium p-4 border-start border-info border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem;">Employees</h6>
                    <h3 class="mb-0 fw-bold">{{ $stats['total_employees'] }}</h3>
                </div>
                <div class="bg-info bg-opacity-10 p-3 rounded-3 text-info">
                    <i class="bi bi-person-badge-fill fs-4"></i>
                </div>
            </div>
            <div class="mt-3 text-muted small">
                Active staff across all branches
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Quick Actions -->
    <div class="col-md-6">
        <div class="card card-premium p-4 h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-lightning-charge me-2 text-indigo"></i>Quick Links</h5>
            <div class="list-group list-group-flush gap-2">
                <a href="{{ route('members') }}" class="list-group-item list-group-item-action border-0 d-flex align-items-center justify-content-between p-3 rounded-3 bg-light">
                    <div>
                        <div class="fw-bold"><i class="bi bi-person-plus-fill me-2 text-primary"></i>Add New Member</div>
                        <small class="text-muted">Register a member in the CRM system</small>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
                <a href="{{ route('memberships') }}" class="list-group-item list-group-item-action border-0 d-flex align-items-center justify-content-between p-3 rounded-3 bg-light">
                    <div>
                        <div class="fw-bold"><i class="bi bi-credit-card-2-front-fill me-2 text-success"></i>Enroll Member in Plan</div>
                        <small class="text-muted">Activate a subscription or renew plan</small>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
                <a href="{{ route('employees') }}" class="list-group-item list-group-item-action border-0 d-flex align-items-center justify-content-between p-3 rounded-3 bg-light">
                    <div>
                        <div class="fw-bold"><i class="bi bi-person-badge-fill me-2 text-info"></i>Assign Gym Trainer</div>
                        <small class="text-muted">Connect active member to personal coach</small>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Active Plans & Overview -->
    <div class="col-md-6">
        <div class="card card-premium p-4 h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-shop me-2 text-indigo"></i>Fitness Center System Info</h5>
            <div class="p-3 bg-light rounded-3 mb-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted">Auth Service Port:</span>
                    <span class="badge bg-secondary">8001</span>
                </div>
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted">CRM Service Port:</span>
                    <span class="badge bg-secondary">8002</span>
                </div>
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted">Membership Service Port:</span>
                    <span class="badge bg-secondary">8003</span>
                </div>
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted">HR Service Port:</span>
                    <span class="badge bg-secondary">8004</span>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <span class="text-muted">Reporting Service Port:</span>
                    <span class="badge bg-secondary">8005</span>
                </div>
            </div>
            <div class="alert alert-info border-0 shadow-sm rounded-3 mb-0" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i>This portal communicates directly with all FitLife backend microservices via JWT bearer token authentication.
            </div>
        </div>
    </div>
</div>
@endsection
