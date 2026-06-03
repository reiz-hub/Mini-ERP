@extends('layouts.app')

@section('title', 'Members - FitLife ERP')
@section('page-title', 'CRM Member Management')

@section('content')
<div class="row g-4">
    <!-- Form Panel: Create or Edit -->
    <div class="col-md-4">
        <div class="card card-premium p-4">
            <h5 class="fw-bold mb-3">
                <i class="bi {{ $editMember ? 'bi-pencil-square' : 'bi-person-plus-fill' }} me-2 text-indigo"></i>
                {{ $editMember ? 'Edit Member details' : 'Add New Member' }}
            </h5>

            @if ($errors->any())
                <div class="alert alert-danger border-0 rounded-3 small">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ $editMember ? route('members.update', $editMember['id']) : route('members.create') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label small text-muted fw-bold text-uppercase">Full Name</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="John Doe" value="{{ old('name', $editMember['name'] ?? '') }}" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label small text-muted fw-bold text-uppercase">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="john@example.com" value="{{ old('email', $editMember['email'] ?? '') }}" required>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label small text-muted fw-bold text-uppercase">Phone Number</label>
                    <input type="text" name="phone" id="phone" class="form-control" placeholder="555-0199" value="{{ old('phone', $editMember['phone'] ?? '') }}" required>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label small text-muted fw-bold text-uppercase">Residential Address</label>
                    <textarea name="address" id="address" class="form-control" rows="2" placeholder="123 Main St" required>{{ old('address', $editMember['address'] ?? '') }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="emergency_contact" class="form-label small text-muted fw-bold text-uppercase">Emergency Contact Name</label>
                    <input type="text" name="emergency_contact" id="emergency_contact" class="form-control" placeholder="Jane Doe" value="{{ old('emergency_contact', $editMember['emergency_contact'] ?? '') }}" required>
                </div>

                <div class="mb-3">
                    <label for="emergency_phone" class="form-label small text-muted fw-bold text-uppercase">Emergency Contact Phone</label>
                    <input type="text" name="emergency_phone" id="emergency_phone" class="form-control" placeholder="555-0198" value="{{ old('emergency_phone', $editMember['emergency_phone'] ?? '') }}" required>
                </div>

                <div class="mb-3">
                    <label for="health_notes" class="form-label small text-muted fw-bold text-uppercase">Health Notes (Optional)</label>
                    <textarea name="health_notes" id="health_notes" class="form-control" rows="2" placeholder="e.g. Asthma, Knee recovery">{{ old('health_notes', $editMember['health_notes'] ?? '') }}</textarea>
                </div>

                @if($editMember)
                    <div class="mb-3">
                        <label for="status" class="form-label small text-muted fw-bold text-uppercase">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="active" {{ (old('status', $editMember['status'] ?? '') === 'active') ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ (old('status', $editMember['status'] ?? '') === 'inactive') ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                @endif

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-indigo flex-grow-1">
                        {{ $editMember ? 'Update Member' : 'Register Member' }}
                    </button>
                    @if($editMember)
                        <a href="{{ route('members') }}" class="btn btn-outline-secondary">Cancel</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Table Panel: Members List -->
    <div class="col-md-8">
        <div class="card card-premium p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0">Registered Members</h5>
                <span class="badge bg-primary rounded-pill px-3">{{ count($members) }} Members</span>
            </div>

            @if(empty($members))
                <div class="text-center py-5">
                    <i class="bi bi-people text-muted fs-1 mb-2"></i>
                    <p class="text-muted">No members registered yet. Fill in the form to register the first member.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Emergency Contact</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($members as $member)
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $member['name'] }}</div>
                                        <small class="text-muted">{{ $member['email'] }}</small>
                                    </td>
                                    <td>
                                        <div class="small"><i class="bi bi-telephone-fill text-muted me-1"></i>{{ $member['phone'] }}</div>
                                        <div class="small text-muted"><i class="bi bi-geo-alt-fill text-muted me-1"></i>{{ Str::limit($member['address'], 30) }}</div>
                                    </td>
                                    <td>
                                        <div class="small fw-medium">{{ $member['emergency_contact'] }}</div>
                                        <small class="text-muted">{{ $member['emergency_phone'] }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $member['status'] ?? 'inactive' }} rounded-pill px-2.5 py-1 text-uppercase">
                                            {{ $member['status'] ?? 'inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('members', ['edit_id' => $member['id']]) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                                <i class="bi bi-pencil me-1"></i>Edit
                                            </a>
                                            <form action="{{ route('members.delete', $member['id']) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this member?');" class="mb-0">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                                    <i class="bi bi-trash me-1"></i>Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
