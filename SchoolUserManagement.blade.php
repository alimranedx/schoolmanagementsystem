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
            <span class="small text-muted ms-2">Manage school info, logo, banner</span>
        </div>
        <div class="card-body">
            <form id="profile-form" class="row g-3" enctype="multipart/form-data">
                <div class="col-md-6">
                    <label class="form-label">School Name</label>
                    <input type="text" class="form-control" name="name" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Academic Year</label>
                    <input type="text" class="form-control" name="academic_year" placeholder="2025-2026">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Address</label>
                    <input type="text" class="form-control" name="address">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Phone</label>
                    <input type="text" class="form-control" name="phone">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Website</label>
                    <input type="url" class="form-control" name="website" placeholder="https://example.com">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Established Year</label>
                    <input type="number" class="form-control" name="established_year" min="1800" max="{{ now()->year + 1 }}">
                </div>

                <div class="col-12">
                    <label class="form-label">About</label>
                    <textarea class="form-control" name="about" rows="3"></textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Logo</label>
                    <input type="file" class="form-control" name="logo" accept="image/*">
                    <div class="mt-2">
                        <img id="logo-preview" src="" alt="Logo Preview" class="img-thumbnail" style="max-height: 120px; display:none;">
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Banner Image</label>
                    <input type="file" class="form-control" name="banner_image" accept="image/*">
                    <div class="mt-2">
                        <img id="banner-preview" src="" alt="Banner Preview" class="img-thumbnail" style="max-height: 160px; display:none;">
                    </div>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Save Profile
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="btn-profile-reload">
                        <i class="bi bi-arrow-clockwise me-1"></i> Reload
                    </button>
                    <div id="profile-status" class="ms-auto small text-muted"></div>
                </div>
            </form>
        </div>
    </div>

    <div id="school-holidays" class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white d-flex align-items-center justify-content-between">
            <div>
                <strong>Holiday Settings</strong>
                <div class="small text-muted">Add or remove holidays for the academic year</div>
            </div>
        </div>
        <div class="card-body">
            <form id="holiday-form" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Date</label>
                    <input type="date" class="form-control" id="holiday-date">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Title</label>
                    <input type="text" class="form-control" id="holiday-title" placeholder="e.g., Independence Day">
                </div>
                <div class="col-md-2 d-grid">
                    <button type="button" id="btn-add-holiday" class="btn btn-outline-primary">
                        <i class="bi bi-plus-lg me-1"></i> Add
                    </button>
                </div>
            </form>

            <div class="table-responsive mt-3">
                <table class="table table-sm table-striped align-middle">
                    <thead>
                        <tr>
                            <th style="width: 160px;">Date</th>
                            <th>Title</th>
                            <th class="text-end" style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="holidays-body">
                        <tr><td colspan="3" class="text-center py-3 text-muted">No holidays</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="d-flex">
                <button type="button" id="btn-save-holidays" class="btn btn-primary ms-auto">
                    <i class="bi bi-save me-1"></i> Save Holidays
                </button>
            </div>
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
    const isFormData = (body instanceof FormData);
    const headers = isFormData ? { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
                               : { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' };
    const res = await fetch(url, {
        method,
        headers,
        body: isFormData ? body : (body ? JSON.stringify(body) : null)
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

async function reload(){
    document.getElementById('pending-body').innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">Loading…</td></tr>';
    document.getElementById('users-body').innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">Loading…</td></tr>';
    try {
        const data = await fetchUsers(1);
        render(data);
    } catch (e) {
        console.error(e);
    }
}

// ===== School Profile & Holidays =====
let PROFILE = null;

function setImagePreview(imgEl, url){
    if (!imgEl) return;
    if (url) { imgEl.src = url; imgEl.style.display = 'inline-block'; }
    else { imgEl.src = ''; imgEl.style.display = 'none'; }
}

function fillProfileForm(profile){
    const form = document.getElementById('profile-form');
    form.name.value = profile.name || '';
    form.academic_year.value = profile.academic_year || '';
    form.address.value = profile.address || '';
    form.phone.value = profile.phone || '';
    form.email.value = profile.email || '';
    form.website.value = profile.website || '';
    form.about.value = profile.about || '';
    form.established_year.value = profile.established_year || '';
    setImagePreview(document.getElementById('logo-preview'), profile.logo_path);
    setImagePreview(document.getElementById('banner-preview'), profile.banner_image_path);
}

function renderHolidays(list){
    const body = document.getElementById('holidays-body');
    if (!list || !list.length){
        body.innerHTML = '<tr><td colspan="3" class="text-center py-3 text-muted">No holidays</td></tr>';
        return;
    }
    const rows = list
        .slice()
        .sort((a,b)=> (a.date||'').localeCompare(b.date||''))
        .map((h, idx)=>`
            <tr>
                <td>${h.date || ''}</td>
                <td>${h.title ? h.title.replace(/</g,'&lt;') : ''}</td>
                <td class="text-end">
                    <button class="btn btn-sm btn-outline-danger" onclick="removeHoliday(${idx})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    body.innerHTML = rows;
}

async function loadProfile(){
    const status = document.getElementById('profile-status');
    try {
        status.textContent = 'Loading profile…';
        const res = await fetch('/school/profile', { headers: { Accept: 'application/json' }});
        if(!res.ok) throw new Error('Failed to load profile');
        PROFILE = await res.json();
        fillProfileForm(PROFILE);
        renderHolidays(PROFILE.holidays || []);
        status.textContent = 'Profile loaded';
        setTimeout(()=> status.textContent = '', 1500);
    } catch (e){
        console.error(e);
        status.textContent = 'Error loading profile';
    }
}

function addHoliday(){
    const date = document.getElementById('holiday-date').value;
    const title = document.getElementById('holiday-title').value.trim();
    if (!date || !title) return;
    PROFILE = PROFILE || {};
    PROFILE.holidays = PROFILE.holidays || [];
    PROFILE.holidays.push({ date, title });
    renderHolidays(PROFILE.holidays);
    document.getElementById('holiday-date').value = '';
    document.getElementById('holiday-title').value = '';
}

function removeHoliday(index){
    if (!PROFILE || !PROFILE.holidays) return;
    PROFILE.holidays.splice(index, 1);
    renderHolidays(PROFILE.holidays);
}

async function saveHolidays(){
    if (!PROFILE) PROFILE = {};
    // Submit only holidays (keep other fields unchanged by refetching form values to avoid wiping)
    const form = document.getElementById('profile-form');
    const fd = new FormData();
    fd.append('name', form.name.value); // required
    fd.append('academic_year', form.academic_year.value || '');
    fd.append('address', form.address.value || '');
    fd.append('phone', form.phone.value || '');
    fd.append('email', form.email.value || '');
    fd.append('website', form.website.value || '');
    fd.append('about', form.about.value || '');
    fd.append('established_year', form.established_year.value || '');
    // holidays
    const holidays = PROFILE.holidays || [];
    holidays.forEach((h, i) => {
        fd.append(`holidays[${i}][date]`, h.date || '');
        fd.append(`holidays[${i}][title]`, h.title || '');
    });

    const status = document.getElementById('profile-status');
    try {
        status.textContent = 'Saving holidays…';
        await post('/school/profile', 'POST', fd);
        status.textContent = 'Holidays saved';
        await loadProfile();
    } catch (e){
        console.error(e);
        status.textContent = 'Failed to save holidays';
    } finally {
        setTimeout(()=> status.textContent = '', 1500);
    }
}

async function saveProfile(e){
    e.preventDefault();
    const form = document.getElementById('profile-form');
    const status = document.getElementById('profile-status');
    const fd = new FormData(form);
    // add holidays array to same request so they stay in sync
    const holidays = PROFILE && PROFILE.holidays ? PROFILE.holidays : [];
    holidays.forEach((h, i) => {
        fd.append(`holidays[${i}][date]`, h.date || '');
        fd.append(`holidays[${i}][title]`, h.title || '');
    });

    try {
        status.textContent = 'Saving profile…';
        await post('/school/profile', 'POST', fd);
        status.textContent = 'Profile saved';
        await loadProfile();
    } catch (e){
        console.error(e);
        status.textContent = 'Failed to save profile';
    } finally {
        setTimeout(()=> status.textContent = '', 1500);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    reload();

    document.getElementById('btn-refresh').addEventListener('click', reload);

    // profile events
    document.getElementById('profile-form').addEventListener('submit', saveProfile);
    document.getElementById('btn-profile-reload').addEventListener('click', loadProfile);

    const logoInput = document.querySelector('input[name="logo"]');
    const bannerInput = document.querySelector('input[name="banner_image"]');
    if (logoInput) logoInput.addEventListener('change', (e)=>{
        const file = e.target.files && e.target.files[0];
        setImagePreview(document.getElementById('logo-preview'), file ? URL.createObjectURL(file) : (PROFILE?.logo_path || ''));
    });
    if (bannerInput) bannerInput.addEventListener('change', (e)=>{
        const file = e.target.files && e.target.files[0];
        setImagePreview(document.getElementById('banner-preview'), file ? URL.createObjectURL(file) : (PROFILE?.banner_image_path || ''));
    });

    document.getElementById('btn-add-holiday').addEventListener('click', addHoliday);
    document.getElementById('btn-save-holidays').addEventListener('click', saveHolidays);

    loadProfile();
});
</script>
@endsection
