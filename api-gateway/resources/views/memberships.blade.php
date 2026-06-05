@extends('layouts.app')

@section('title', 'Memberships - FitLife ERP')
@section('page-title', 'Membership & Subscription Management')

@section('content')
<!-- Plans CRUD & Enrollment Form Row -->
<div class="row g-4 mb-4">
    <!-- Form 1: Create subscription plan -->
    <div class="col-md-4">
        <div class="card card-premium p-4 h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-tag-fill me-2 text-indigo"></i>Create Subscription Plan</h5>
            <form action="{{ route('plans.create') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="plan_name" class="form-label small text-muted fw-bold text-uppercase">Plan Name</label>
                    <input type="text" name="name" id="plan_name" class="form-control" placeholder="e.g. Standard Monthly" required>
                </div>
                <div class="mb-3">
                    <label for="duration_months" class="form-label small text-muted fw-bold text-uppercase">Duration (Months)</label>
                    <input type="number" name="duration_months" id="duration_months" class="form-control" min="1" placeholder="e.g. 1" required>
                </div>
                <div class="mb-3">
                    <label for="price" class="form-label small text-muted fw-bold text-uppercase">Price (₱)</label>
                    <input type="number" step="0.01" name="price" id="price" class="form-control" min="0" placeholder="e.g. 49.99" required>
                </div>
                <div class="mb-3">
                    <label for="plan_desc" class="form-label small text-muted fw-bold text-uppercase">Description</label>
                    <textarea name="description" id="plan_desc" class="form-control" rows="2" placeholder="Plan inclusions..."></textarea>
                </div>
                <div class="mb-3">
                    <label for="plan_status" class="form-label small text-muted fw-bold text-uppercase">Status</label>
                    <select name="status" id="plan_status" class="form-select">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-indigo w-100">Create Plan</button>
            </form>
        </div>
    </div>

    <!-- Form 2: Enroll Member in Plan -->
    <div class="col-md-4">
        <div class="card card-premium p-4 h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-person-check-fill me-2 text-indigo"></i>Enroll Member in Plan</h5>
            <form action="{{ route('memberships.enroll') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="enroll_member" class="form-label small text-muted fw-bold text-uppercase">Select Member</label>
                    <select name="member_id" id="enroll_member" class="form-select" required>
                        <option value="">-- Choose Member --</option>
                        @foreach ($members as $m)
                            <option value="{{ $m['id'] }}">{{ $m['name'] }} ({{ $m['email'] }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="enroll_plan" class="form-label small text-muted fw-bold text-uppercase">Select Plan</label>
                    <select name="plan_id" id="enroll_plan" class="form-select" required>
                        <option value="">-- Choose Plan --</option>
                        @foreach ($plans as $p)
                            @if (($p['status'] ?? 'active') === 'active')
                                <option value="{{ $p['id'] }}" data-price="{{ $p['price'] }}">{{ $p['name'] }} - ₱{{ $p['price'] }} ({{ $p['duration_months'] }} mo)</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="start_date" class="form-label small text-muted fw-bold text-uppercase">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="mb-3">
                    <label for="amount_paid" class="form-label small text-muted fw-bold text-uppercase">Amount Paid (₱)</label>
                    <input type="number" step="0.01" name="amount_paid" id="amount_paid" class="form-control" min="0" placeholder="0.00" required>
                </div>
                <button type="submit" class="btn btn-indigo w-100">Enroll Member</button>
            </form>
        </div>
    </div>

    <!-- Subscriptions Plans List -->
    <div class="col-md-4">
        <div class="card card-premium p-4 h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-journal-text me-2 text-indigo"></i>Available Plans</h5>
            @if (empty($plans))
                <p class="text-muted small">No subscription plans created yet.</p>
            @else
                <div class="list-group list-group-flush" style="max-height: 280px; overflow-y: auto;">
                    @foreach ($plans as $p)
                        <div class="list-group-item border-0 p-3 bg-light rounded-3 mb-2">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="fw-bold text-dark">{{ $p['name'] }}</div>
                                <span class="badge bg-indigo">₱{{ number_format($p['price'], 2) }}</span>
                            </div>
                            <div class="small text-muted mt-1">
                                Duration: {{ $p['duration_months'] }} Month(s) | Status:
                                <span class="fw-bold text-{{ ($p['status'] ?? 'active') === 'active' ? 'success' : 'danger' }}">{{ $p['status'] ?? 'active' }}</span>
                            </div>
                            @if($p['description'])
                                <div class="small text-muted text-truncate mt-1">{{ $p['description'] }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Active Memberships List Panel -->
<div class="card card-premium p-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="fw-bold mb-0">Active Memberships & Subscriptions</h5>
        <span class="badge bg-primary rounded-pill px-3">{{ count($memberships) }} Subscriptions</span>
    </div>

    @if (empty($memberships))
        <div class="text-center py-5">
            <i class="bi bi-credit-card text-muted fs-1 mb-2"></i>
            <p class="text-muted">No memberships registered yet. Select a member and plan above to enroll.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Member ID</th>
                        <th>Plan Details</th>
                        <th>Validity Period</th>
                        <th>Amount Paid</th>
                        <th>Status</th>
                        <th class="text-center" style="width: 250px;">Renew</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($memberships as $m)
                        <tr>
                            <td>
                                <div class="fw-semibold text-dark">Member #{{ $m['member_id'] }}</div>
                                <!-- Helper to find name if possible -->
                                @php
                                    $mbr = Collect($members)->firstWhere('id', $m['member_id']);
                                @endphp
                                @if($mbr)
                                    <small class="text-muted">{{ $mbr['name'] }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $m['plan']['name'] ?? 'N/A' }}</div>
                                <small class="text-muted">Plan ID: {{ $m['plan_id'] }}</small>
                            </td>
                            <td>
                                <div class="small"><span class="text-muted">From:</span> {{ $m['start_date'] }}</div>
                                <div class="small"><span class="text-muted">To:</span> {{ $m['end_date'] }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">₱{{ number_format($m['amount_paid'], 2) }}</div>
                            </td>
                            <td>
                                <span class="badge badge-{{ $m['status'] }} rounded-pill px-2.5 py-1 text-uppercase">
                                    {{ $m['status'] }}
                                </span>
                            </td>
                            <td>
                                <form action="{{ route('memberships.renew', $m['id']) }}" method="POST" class="d-flex gap-2 align-items-center mb-0">
                                    @csrf
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" step="0.01" name="amount_paid" class="form-control" placeholder="{{ $m['plan']['price'] ?? '0.00' }}" style="max-width: 90px;" required>
                                    </div>
                                    <button type="submit" class="btn btn-outline-success btn-sm rounded-pill px-3 text-nowrap">
                                        <i class="bi bi-arrow-repeat me-1"></i>Renew
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const planSelect = document.getElementById('enroll_plan');
        const amountInput = document.getElementById('amount_paid');

        planSelect.addEventListener('change', function() {
            const selectedOption = planSelect.options[planSelect.selectedIndex];
            const price = selectedOption.getAttribute('data-price');
            if (price) {
                amountInput.value = price;
            } else {
                amountInput.value = '';
            }
        });
    });
</script>
@endsection
