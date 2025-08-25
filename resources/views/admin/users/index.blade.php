@extends('layouts.admin')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="mb-0">User Management</h4>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card mb-3">
    <div class="card-body">
        <form class="row g-2 align-items-end" method="get" action="{{ url('/admin/users') }}">
            <div class="col-sm-6 col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Name or Email">
            </div>
            <div class="col-sm-6 col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="pending" {{ $status==='pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ $status==='approved' ? 'selected' : '' }}>Approved</option>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-primary"><i class="bi bi-search"></i> Filter</button>
            </div>
            <div class="col-auto">
                <a href="{{ url('/admin/users') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

@if(!isset($mode) || $mode !== 'all')
<!-- Pending Users Section -->
<div id="pending-section" class="card mb-4">
    <div class="card-header d-flex align-items-center">
        <i class="bi bi-hourglass-split me-2"></i>
        <div class="fw-semibold">Pending Users</div>
        <span class="badge bg-secondary ms-2">{{ $pendingUsers->total() }}</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Requested Role</th>
                    <th>Roles</th>
                    <th>Status</th>
                    <th>Active</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingUsers as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td><span class="badge text-bg-warning">{{ $user->pending_role ?? 'N/A' }}</span></td>
                        <td>
                            @forelse($user->roles as $role)
                                <span class="badge text-bg-info me-1 role-badge">{{ $role->name }}</span>
                            @empty
                                <span class="text-muted small">None</span>
                            @endforelse
                        </td>
                        <td><span class="badge text-bg-secondary">{{ $user->status }}</span></td>
                        <td>
                            @if($user->is_active)
                                <span class="badge text-bg-success">Active</span>
                            @else
                                <span class="badge text-bg-light text-dark">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <form class="d-inline" method="post" action="{{ url('/admin/users/'.$user->id.'/approve') }}">
                                @csrf
                                <button class="btn btn-sm btn-success" title="Approve"><i class="bi bi-check2-circle"></i></button>
                            </form>
                            @if($user->is_active)
                                <form class="d-inline" method="post" action="{{ url('/admin/users/'.$user->id.'/deactivate') }}">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-warning" title="Deactivate"><i class="bi bi-pause"></i></button>
                                </form>
                            @else
                                <form class="d-inline" method="post" action="{{ url('/admin/users/'.$user->id.'/activate') }}">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-success" title="Activate"><i class="bi bi-play"></i></button>
                                </form>
                            @endif
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editUserModal-{{ $user->id }}" title="Edit"><i class="bi bi-pencil"></i></button>
                            <form class="d-inline" method="post" action="{{ url('/admin/users/'.$user->id) }}" onsubmit="return confirm('Delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editUserModal-{{ $user->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit User #{{ $user->id }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="post" action="{{ url('/admin/users/'.$user->id) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Name</label>
                                            <input type="text" name="name" class="form-control" value="{{ $user->name }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" name="email" class="form-control" value="{{ $user->email }}">
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <label class="form-label">Status</label>
                                                <select name="status" class="form-select">
                                                    <option value="pending" {{ $user->status==='pending'?'selected':'' }}>pending</option>
                                                    <option value="approved" {{ $user->status==='approved'?'selected':'' }}>approved</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Active</label>
                                                <select name="is_active" class="form-select">
                                                    <option value="1" {{ $user->is_active? 'selected' : '' }}>Yes</option>
                                                    <option value="0" {{ !$user->is_active? 'selected' : '' }}>No</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button class="btn btn-primary">Save changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr><td colspan="8" class="text-center text-muted">No pending users.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body border-top">
        {{ $pendingUsers->links() }}
    </div>
</div>
@endif

@if(!isset($mode) || $mode !== 'pending')
<!-- All Users Section -->
<div id="users-section" class="card">
    <div class="card-header d-flex align-items-center">
        <i class="bi bi-list-check me-2"></i>
        <div class="fw-semibold">All Users</div>
        <span class="badge bg-secondary ms-2">{{ $users->total() }}</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Roles</th>
                    <th>Status</th>
                    <th>Active</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @forelse($user->roles as $role)
                                <span class="badge text-bg-info me-1 role-badge">{{ $role->name }}</span>
                            @empty
                                <span class="text-muted small">None</span>
                            @endforelse
                        </td>
                        <td>
                            <span class="badge {{ $user->status==='approved' ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $user->status }}</span>
                        </td>
                        <td>
                            @if($user->is_active)
                                <span class="badge text-bg-success">Active</span>
                            @else
                                <span class="badge text-bg-light text-dark">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($user->is_active)
                                <form class="d-inline" method="post" action="{{ url('/admin/users/'.$user->id.'/deactivate') }}">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-warning" title="Deactivate"><i class="bi bi-pause"></i></button>
                                </form>
                            @else
                                <form class="d-inline" method="post" action="{{ url('/admin/users/'.$user->id.'/activate') }}">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-success" title="Activate"><i class="bi bi-play"></i></button>
                                </form>
                            @endif
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editUserModalAll-{{ $user->id }}" title="Edit"><i class="bi bi-pencil"></i></button>
                            <form class="d-inline" method="post" action="{{ url('/admin/users/'.$user->id) }}" onsubmit="return confirm('Delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <!-- Edit Modal for All Users -->
                    <div class="modal fade" id="editUserModalAll-{{ $user->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit User #{{ $user->id }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="post" action="{{ url('/admin/users/'.$user->id) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Name</label>
                                            <input type="text" name="name" class="form-control" value="{{ $user->name }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" name="email" class="form-control" value="{{ $user->email }}">
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <label class="form-label">Status</label>
                                                <select name="status" class="form-select">
                                                    <option value="pending" {{ $user->status==='pending'?'selected':'' }}>pending</option>
                                                    <option value="approved" {{ $user->status==='approved'?'selected':'' }}>approved</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Active</label>
                                                <select name="is_active" class="form-select">
                                                    <option value="1" {{ $user->is_active? 'selected' : '' }}>Yes</option>
                                                    <option value="0" {{ !$user->is_active? 'selected' : '' }}>No</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button class="btn btn-primary">Save changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr><td colspan="7" class="text-center text-muted">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body border-top">
        {{ $users->links() }}
    </div>
</div>
@endif
@endsection
