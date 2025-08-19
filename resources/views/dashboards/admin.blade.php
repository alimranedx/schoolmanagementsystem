@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="text-muted small">Total Users</div>
                    <div class="fs-4 fw-bold" id="stat-total">–</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="text-muted small">Pending Approvals</div>
                    <div class="fs-4 fw-bold text-warning" id="stat-pending">–</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="text-muted small">Active</div>
                    <div class="fs-4 fw-bold text-success" id="stat-active">–</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="text-muted small">Inactive</div>
                    <div class="fs-4 fw-bold text-danger" id="stat-inactive">–</div>
                </div>
            </div>
        </div>
    </div>

    <div id="pending-section" class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white d-flex align-items-center justify-content-between">
            <div>
                <strong>Pending Registrations</strong>
                <div class="text-muted small">Approve users to allow login</div>
            </div>
            <button id="btn-refresh" class="btn btn-outline-primary btn-sm">Refresh</button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Requested Role</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="pending-body">
                        <tr><td colspan="5" class="text-center py-4 text-muted">Loading…</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="users-section" class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <strong>All Users</strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Roles</th>
                            <th>Status</th>
                            <th>Active</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="users-body">
                        <tr><td colspan="6" class="text-center py-4 text-muted">Loading…</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="school-profile" class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white d-flex align-items-center">
            <strong>School Profile</strong>
            <span class="small text-muted ms-2">(read-only placeholder)</span>
        </div>
        <div class="card-body">
            <p class="text-muted mb-0">You can manage school name, logo, academic year, and holidays here in the future.</p>
        </div>
    </div>

    <div id="notifications" class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white">
            <strong>Notifications</strong>
        </div>
        <div class="card-body">
            <p class="text-muted mb-0">Send system notifications to users (feature ready via API, UI coming soon).</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const ROLES = ['admin','teacher','student','parent','staff'];
let pagination = { current_page: 1, last_page: 1 };

function roleBadges(user){
    if (!user.roles || user.roles.length === 0) return '<span class="badge text-bg-secondary">none</span>';
    return user.roles.map(r=>`<span class="badge text-bg-primary role-badge me-1">${r.name}</span>`).join('');
}

function statusBadge(user){
    const s = user.status;
    const map = { approved: 'success', pending: 'warning' };
    const cls = map[s] || 'secondary';
    return `<span class="badge text-bg-${cls}">${s}</span>`;
}

function activeBadge(user){
    return user.is_active ? '<span class="badge text-bg-success">active</span>' : '<span class="badge text-bg-danger">inactive</span>';
}

async function fetchUsers(page=1){
    const res = await fetch(`/admin/users?page=${page}`, { headers: { 'Accept': 'application/json' } });
    if(!res.ok){ throw new Error('Failed to load users'); }
    return res.json();
}

function render(usersPage){
    const data = usersPage.data || [];
    // Stats
    const total = usersPage.total ?? data.length;
    const pending = data.filter(u=>u.status==='pending').length;
    const active = data.filter(u=>u.is_active).length;
    const inactive = data.filter(u=>!u.is_active).length;
    document.getElementById('stat-total').textContent = total;
    document.getElementById('stat-pending').textContent = pending;
    document.getElementById('stat-active').textContent = active;
    document.getElementById('stat-inactive').textContent = inactive;

    // Pending table
    const pendingUsers = data.filter(u=>u.status==='pending');
    const pendingBody = document.getElementById('pending-body');
    pendingBody.innerHTML = pendingUsers.length ? pendingUsers.map(u=>`
        <tr>
            <td>${u.name}</td>
            <td>${u.email}</td>
            <td>${u.pending_role ? `<span class=\"badge text-bg-info\">${u.pending_role}</span>` : '<span class=\"text-muted\">n/a</span>'}</td>
            <td>${statusBadge(u)}</td>
            <td class="text-end">
                <button class="btn btn-success btn-sm me-2" onclick="approve(${u.id})">Approve</button>
                <button class="btn btn-outline-danger btn-sm" onclick="removeUser(${u.id})">Delete</button>
            </td>
        </tr>`).join('') : '<tr><td colspan="5" class="text-center py-4 text-muted">No pending registrations</td></tr>';

    // All users table
    const usersBody = document.getElementById('users-body');
    usersBody.innerHTML = data.map(u=>{
        const roleOptions = ROLES.map(r=>`<option value="${r}">${r}</option>`).join('');
        return `<tr>
            <td>${u.name}</td>
            <td>${u.email}</td>
            <td>${roleBadges(u)}</td>
            <td>${statusBadge(u)}</td>
            <td>${activeBadge(u)}</td>
            <td class="text-end">
                <div class="btn-group me-2">
                    <button class="btn btn-outline-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">Assign Role</button>
                    <div class="dropdown-menu dropdown-menu-end p-2" style="min-width: 200px;">
                        <div class="input-group input-group-sm">
                            <select class="form-select" id="assign-${u.id}">${roleOptions}</select>
                            <button class="btn btn-primary" onclick="assignRole(${u.id})">Add</button>
                        </div>
                    </div>
                </div>
                <div class="btn-group me-2">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">Revoke Role</button>
                    <div class="dropdown-menu dropdown-menu-end">
                        ${ (u.roles||[]).map(r=>`<button class=\"dropdown-item\" onclick=\"revokeRole(${u.id}, '${r.name}')\">${r.name}</button>`).join('') || '<span class="dropdown-item text-muted">No roles</span>'}
                    </div>
                </div>
                ${ u.status==='pending' ? `<button class=\"btn btn-success btn-sm me-2\" onclick=\"approve(${u.id})\">Approve</button>` : '' }
                ${ u.is_active ? `<button class=\"btn btn-warning btn-sm me-2\" onclick=\"deactivate(${u.id})\">Deactivate</button>` : `<button class=\"btn btn-success btn-sm me-2\" onclick=\"activate(${u.id})\">Activate</button>` }
                <button class="btn btn-outline-danger btn-sm" onclick="removeUser(${u.id})">Delete</button>
            </td>
        </tr>`;
    }).join('');
}

async function post(url, method='POST', body=null){
    const res = await fetch(url, {
        method,
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
        body: body ? JSON.stringify(body) : null
    });
    if(!res.ok){
        const msg = await res.text();
        throw new Error(msg || 'Request failed');
    }
    return res.json().catch(()=>({}));
}

async function approve(id){ await post(`/admin/users/${id}/approve`); await reload(); }
async function activate(id){ await post(`/admin/users/${id}/activate`); await reload(); }
async function deactivate(id){ await post(`/admin/users/${id}/deactivate`); await reload(); }
async function assignRole(id){ const select = document.getElementById(`assign-${id}`); if(!select) return; await post(`/admin/users/${id}/assign-role`, 'POST', { role: select.value }); await reload(); }
async function revokeRole(id, role){ await post(`/admin/users/${id}/revoke-role`, 'POST', { role }); await reload(); }
async function removeUser(id){ if(!confirm('Are you sure you want to delete this user?')) return; await fetch(`/admin/users/${id}`, { method:'DELETE', headers:{ 'X-CSRF-TOKEN': CSRF, 'Accept':'application/json' }}); await reload(); }

async function reload(){
    const btn = document.getElementById('btn-refresh');
    const orig = btn.textContent; btn.disabled = true; btn.textContent = 'Refreshing…';
    try {
        const page = await fetchUsers(pagination.current_page);
        pagination = { current_page: page.current_page, last_page: page.last_page };
        render(page);
    } catch(e){
        alert('Failed to load users. Make sure you are logged in as admin.');
        console.error(e);
    } finally {
        btn.disabled = false; btn.textContent = orig;
    }
}

window.addEventListener('DOMContentLoaded', reload);
</script>
@endsection
