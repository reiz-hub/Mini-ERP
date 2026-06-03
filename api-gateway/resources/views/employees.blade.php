@extends('layouts.app')

@section('title', 'Employees - FitLife ERP')
@section('page-title', 'HR Staff & Trainer Assignment')

@section('content')
<!-- Staff Creation & Trainer Assignment Forms Row -->
<div class="row g-4 mb-4">
    <!-- Form 1: Add Employee -->
    <div class="col-md-5">
        <div class="card card-premium p-4 h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-person-badge-fill me-2 text-indigo"></i>Add New Employee</h5>
            @if ($errors->any())
                <div class="alert alert-danger border-0 rounded-3 small">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('employees.create') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="emp_name" class="form-label small text-muted fw-bold text-uppercase">Full Name</label>
                    <input type="text" name="name" id="emp_name" class="form-control" placeholder="e.g. Mike Tyson" required>
                </div>
                <div class="mb-3">
                    <label for="emp_email" class="form-label small text-muted fw-bold text-uppercase">Email Address</label>
                    <input type="email" name="email" id="emp_email" class="form-control" placeholder="mike@fitlife.com" required>
                </div>
                <div class="mb-3">
                    <label for="emp_phone" class="form-label small text-muted fw-bold text-uppercase">Phone Number</label>
                    <input type="text" name="phone" id="emp_phone" class="form-control" placeholder="555-0101" required>
                </div>
                <div class="mb-3">
                    <label for="emp_role" class="form-label small text-muted fw-bold text-uppercase">Role</label>
                    <select name="role" id="emp_role" class="form-select" required>
                        <option value="trainer">Trainer</option>
                        <option value="manager">Manager</option>
                        <option value="front_desk">Front Desk</option>
                        <option value="maintenance">Maintenance staff</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="emp_branch" class="form-label small text-muted fw-bold text-uppercase">Gym Branch</label>
                    <input type="text" name="branch" id="emp_branch" class="form-control" placeholder="e.g. Downtown" required>
                </div>
                <div class="mb-3">
                    <label for="emp_schedule" class="form-label small text-muted fw-bold text-uppercase">Work Schedule</label>
                    <input type="text" name="schedule" id="emp_schedule" class="form-control" placeholder="e.g. Mon-Fri 8 AM - 5 PM" required>
                </div>
                <button type="submit" class="btn btn-indigo w-100">Add Employee</button>
            </form>
        </div>
    </div>

    <!-- Form 2: Assign Trainer to Member -->
    <div class="col-md-7">
        <div class="card card-premium p-4 h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-calendar-check-fill me-2 text-indigo"></i>Assign Trainer to Member</h5>
            <form action="{{ route('assignments.create') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="assign_member" class="form-label small text-muted fw-bold text-uppercase">Select Member</label>
                    <select name="member_id" id="assign_member" class="form-select" required>
                        <option value="">-- Choose Member --</option>
                        @foreach ($members as $m)
                            <option value="{{ $m['id'] }}">{{ $m['name'] }} ({{ $m['email'] }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="assign_trainer" class="form-label small text-muted fw-bold text-uppercase">Select Active Trainer</label>
                    <select name="trainer_id" id="assign_trainer" class="form-select" required>
                        <option value="">-- Choose Trainer --</option>
                        @foreach ($employees as $e)
                            @if ($e['role'] === 'trainer' && ($e['status'] ?? 'active') === 'active')
                                <option value="{{ $e['id'] }}">{{ $e['name'] }} (Branch: {{ $e['branch'] }})</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="assign_schedule" class="form-label small text-muted fw-bold text-uppercase">Training Schedule</label>
                    <input type="text" name="schedule" id="assign_schedule" class="form-control" placeholder="e.g. Tue-Thu 10:00 AM" required>
                </div>
                <div class="mb-3">
                    <label for="assign_notes" class="form-label small text-muted fw-bold text-uppercase">Training Notes / Goals</label>
                    <textarea name="notes" id="assign_notes" class="form-control" rows="3" placeholder="Fitness goals, health considerations..."></textarea>
                </div>
                <button type="submit" class="btn btn-indigo w-100">Create Trainer Assignment</button>
            </form>
        </div>
    </div>
</div>

<!-- Employee List Panel -->
<div class="card card-premium p-4 mb-4">
    <h5 class="fw-bold mb-3">Staff List</h5>
    @if (empty($employees))
        <p class="text-muted text-center py-4">No employees registered yet.</p>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Employee Name</th>
                        <th>Contact Info</th>
                        <th>Role</th>
                        <th>Branch</th>
                        <th>Schedule</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employees as $e)
                        <tr>
                            <td class="fw-semibold text-dark">{{ $e['name'] }}</td>
                            <td>
                                <div class="small">{{ $e['email'] }}</div>
                                <div class="small text-muted">{{ $e['phone'] }}</div>
                            </td>
                            <td>
                                <span class="badge bg-secondary text-capitalize">{{ str_replace('_', ' ', $e['role']) }}</span>
                            </td>
                            <td>{{ $e['branch'] }}</td>
                            <td>{{ $e['schedule'] }}</td>
                            <td>
                                <span class="badge badge-{{ $e['status'] ?? 'active' }} rounded-pill px-2 py-1 text-uppercase">
                                    {{ $e['status'] ?? 'active' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<!-- Trainer Assignments Panel -->
<div class="card card-premium p-4">
    <h5 class="fw-bold mb-3">Active Trainer Assignments</h5>
    @if (empty($assignments))
        <p class="text-muted text-center py-4">No active trainer assignments.</p>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Member</th>
                        <th>Assigned Trainer</th>
                        <th>Training Schedule</th>
                        <th>Notes / Goals</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($assignments as $a)
                        <tr>
                            <td>
                                <div class="fw-semibold text-dark">Member #{{ $a['member_id'] }}</div>
                                @php
                                    $mbr = Collect($members)->firstWhere('id', $a['member_id']);
                                @endphp
                                @if($mbr)
                                    <small class="text-muted">{{ $mbr['name'] }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $a['trainer']['name'] ?? 'N/A' }}</div>
                                <small class="text-muted">ID: {{ $a['trainer_id'] }}</small>
                            </td>
                            <td>{{ $a['schedule'] }}</td>
                            <td>{{ $a['notes'] ?? 'None' }}</td>
                            <td>
                                <span class="badge badge-{{ $a['status'] ?? 'active' }} rounded-pill px-2 py-1 text-uppercase">
                                    {{ $a['status'] ?? 'active' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
