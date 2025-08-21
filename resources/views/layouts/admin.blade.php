<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - {{ config('app.name', 'Laravel') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --sidebar-width: 260px; }
        body { background-color: #f5f6f8; }
        .admin-wrapper { display: flex; min-height: 100vh; }
        .sidebar {
            width: var(--sidebar-width);
            background: #111827; /* slate-900 */
            color: #d1d5db; /* gray-300 */
            overflow-y: auto;
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch; /* smooth scrolling on iOS */
        }
        .sidebar .brand { color: #fff; font-weight: 600; text-decoration: none; }
        .sidebar .nav-link { color: #d1d5db; border-radius: .375rem; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background: #1f2937; }
        .sidebar .nav-link .bi { width: 1.25rem; }
        .content { flex: 1; min-width: 0; }
        .topbar { background: #ffffff; border-bottom: 1px solid #e5e7eb; }
        .stat-card { border: 0; border-radius: 0.75rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05), 0 4px 12px rgba(0,0,0,0.05); }
        .table thead th { background: #fafbfc; }
        .role-badge { text-transform: capitalize; }
        @media (max-width: 991.98px) { /* lg breakpoint */
            .sidebar { position: fixed; top: 0; bottom: 0; left: 0; transform: translateX(-100%); transition: transform .2s ease; z-index: 1031; }
            .sidebar.show { transform: translateX(0); }
            .content { width: 100%; }
        }
        @media (min-width: 992px) {
            .content-inner { margin-left: var(--sidebar-width); }
            .sidebar { position: fixed; top: 0; bottom: 0; }
            .topbar { position: fixed; top: 0; right: 0; left: var(--sidebar-width); z-index: 1030; }
            .main-content { padding-top: 72px; }
        }
    </style>
</head>
<body>
<div class="admin-wrapper">
    <!-- Sidebar -->
    <nav id="sidebar" class="sidebar p-3">
        <div class="d-flex align-items-center mb-3">
            <a class="brand d-flex align-items-center" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-mortarboard-fill me-2"></i> {{ config('app.name', 'Laravel') }}
            </a>
        </div>
        <div class="small text-uppercase text-secondary mb-2">Admin Menu</div>
        <ul class="nav nav-pills flex-column gap-1">
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <button class="nav-link d-flex align-items-center w-100 text-start" data-bs-toggle="collapse" data-bs-target="#menu-users" aria-expanded="true">
                    <i class="bi bi-people me-2"></i> User Management
                    <i class="bi bi-caret-down-fill ms-auto small"></i>
                </button>
                <div id="menu-users" class="collapse show ps-2">
                    <ul class="nav flex-column gap-1 mt-1">
                        <li><a class="nav-link" href="#pending-section"><i class="bi bi-hourglass-split me-2"></i> Pending Approvals</a></li>
                        <li><a class="nav-link" href="#users-section"><i class="bi bi-list-check me-2"></i> All Users</a></li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <button class="nav-link d-flex align-items-center w-100 text-start" data-bs-toggle="collapse" data-bs-target="#menu-school" aria-expanded="{{ request()->routeIs('admin.school.*') ? 'true' : 'false' }}">
                    <i class="bi bi-building me-2"></i> School Settings
                    <i class="bi bi-caret-down-fill ms-auto small"></i>
                </button>
                <div id="menu-school" class="collapse {{ request()->routeIs('admin.school.*') ? 'show' : '' }} ps-2">
                    <ul class="nav flex-column gap-1 mt-1">
                        <li><a class="nav-link {{ request()->routeIs('admin.school.profile') ? 'active' : '' }}" href="{{ route('admin.school.profile') }}"><i class="bi bi-gear me-2"></i> Profile</a></li>
                        <li><a class="nav-link {{ request()->routeIs('admin.school.holidays.*') ? 'active' : '' }}" href="{{ route('admin.school.holidays.index') }}"><i class="bi bi-calendar3 me-2"></i> Holidays</a></li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <button class="nav-link d-flex align-items-center w-100 text-start" data-bs-toggle="collapse" data-bs-target="#menu-rbac" aria-expanded="false">
                    <i class="bi bi-shield-lock me-2"></i> Roles & Permissions
                    <i class="bi bi-caret-down-fill ms-auto small"></i>
                </button>
                <div id="menu-rbac" class="collapse ps-2">
                    <ul class="nav flex-column gap-1 mt-1">
                        <li><a class="nav-link disabled" href="#"><i class="bi bi-person-badge me-2"></i> Roles (coming soon)</a></li>
                        <li><a class="nav-link disabled" href="#"><i class="bi bi-key me-2"></i> Permissions (coming soon)</a></li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center" href="#notifications">
                    <i class="bi bi-bell me-2"></i> Notifications
                </a>
            </li>
            <li class="nav-item mt-2">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-link d-flex align-items-center"><i class="bi bi-box-arrow-right me-2"></i> Logout</button>
                </form>
            </li>
        </ul>
    </nav>

    <!-- Content -->
    <div class="content w-100">
        <div class="topbar py-3 px-3 px-lg-4 d-flex align-items-center justify-content-between">
            <div class="d-lg-none">
                <button class="btn btn-outline-secondary" id="btn-toggle-sidebar"><i class="bi bi-list"></i></button>
            </div>
            <div class="ms-lg-0 fw-semibold">Admin Panel</div>
            <div class="d-none d-lg-block small text-muted">Signed in as {{ auth()->user()->name }}</div>
        </div>
        <div class="main-content px-3 px-lg-4">
            <div class="content-inner">
                @yield('content')
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('btn-toggle-sidebar');
    if (toggle) {
        toggle.addEventListener('click', () => sidebar.classList.toggle('show'));
    }
    // Hide sidebar when clicking outside on small screens
    document.addEventListener('click', (e) => {
        if (window.innerWidth < 992) {
            if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                sidebar.classList.remove('show');
            }
        }
    });
</script>
@yield('scripts')
</body>
</html>
