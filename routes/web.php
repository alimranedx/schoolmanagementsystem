<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SchoolProfileController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\UpazilaController;
use App\Http\Controllers\DivisionController;

Route::get('/', function () {
    return view('welcome');
});

// Public base routes per user type: redirect based on auth status and role
Route::get('/admin', function(){
    if (Auth::check()) {
        $u = Auth::user();
        if (method_exists($u, 'hasRole')) {
            if ($u->hasRole('admin')) return redirect()->route('admin.dashboard');
            if ($u->hasRole('teacher')) return redirect()->route('teachers.dashboard');
            if ($u->hasRole('student')) return redirect()->route('students.dashboard');
            if ($u->hasRole('parent')) return redirect()->route('parents.dashboard');
            if ($u->hasRole('staff')) return redirect()->route('staff.dashboard');
        }
        return redirect()->route('dashboard');
    }
    return redirect()->route('admin.login');
})->name('base.admin');

Route::get('/teachers', function(){
    if (Auth::check()) {
        $u = Auth::user();
        if ($u->hasRole('teacher')) return redirect()->route('teachers.dashboard');
        if ($u->hasRole('admin')) return redirect()->route('admin.dashboard');
        if ($u->hasRole('student')) return redirect()->route('students.dashboard');
        if ($u->hasRole('parent')) return redirect()->route('parents.dashboard');
        if ($u->hasRole('staff')) return redirect()->route('staff.dashboard');
        return redirect()->route('dashboard');
    }
    return redirect()->route('teachers.login');
})->name('base.teachers');

Route::get('/students', function(){
    if (Auth::check()) {
        $u = Auth::user();
        if ($u->hasRole('student')) return redirect()->route('students.dashboard');
        if ($u->hasRole('admin')) return redirect()->route('admin.dashboard');
        if ($u->hasRole('teacher')) return redirect()->route('teachers.dashboard');
        if ($u->hasRole('parent')) return redirect()->route('parents.dashboard');
        if ($u->hasRole('staff')) return redirect()->route('staff.dashboard');
        return redirect()->route('dashboard');
    }
    return redirect()->route('students.login');
})->name('base.students');

Route::get('/parents', function(){
    if (Auth::check()) {
        $u = Auth::user();
        if ($u->hasRole('parent')) return redirect()->route('parents.dashboard');
        if ($u->hasRole('admin')) return redirect()->route('admin.dashboard');
        if ($u->hasRole('teacher')) return redirect()->route('teachers.dashboard');
        if ($u->hasRole('student')) return redirect()->route('students.dashboard');
        if ($u->hasRole('staff')) return redirect()->route('staff.dashboard');
        return redirect()->route('dashboard');
    }
    return redirect()->route('parents.login');
})->name('base.parents');

Route::get('/staff', function(){
    if (Auth::check()) {
        $u = Auth::user();
        if ($u->hasRole('staff')) return redirect()->route('staff.dashboard');
        if ($u->hasRole('admin')) return redirect()->route('admin.dashboard');
        if ($u->hasRole('teacher')) return redirect()->route('teachers.dashboard');
        if ($u->hasRole('student')) return redirect()->route('students.dashboard');
        if ($u->hasRole('parent')) return redirect()->route('parents.dashboard');
        return redirect()->route('dashboard');
    }
    return redirect()->route('staff.login');
})->name('base.staff');

// Authentication routes (Bootstrap views)
Route::middleware('web')->group(function () {
    // Generic login/register act as a selector or fallback
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('guest');

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('guest');

    // Role-specific login/register
    Route::get('/login/{type}', [AuthController::class, 'showLoginForm'])->whereIn('type', ['admin','teacher','student','parent','staff'])->name('login.type');
    Route::post('/login/{type}', [AuthController::class, 'login'])->whereIn('type', ['admin','teacher','student','parent','staff'])->middleware('guest');

    Route::get('/register/{type}', [AuthController::class, 'showRegisterForm'])->whereIn('type', ['admin','teacher','student','parent','staff'])->name('register.type');
    Route::post('/register/{type}', [AuthController::class, 'register'])->whereIn('type', ['admin','teacher','student','parent','staff'])->middleware('guest');

    // Prefixed per-type auth under base paths
    Route::prefix('admin')->group(function(){
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('admin.login')->defaults('type','admin');
        Route::post('/login', [AuthController::class, 'login'])->middleware('guest')->defaults('type','admin');
        Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('admin.register')->defaults('type','admin');
        Route::post('/register', [AuthController::class, 'register'])->middleware('guest')->defaults('type','admin');
    });
    // Plural auth paths for non-admin roles
    Route::prefix('teachers')->group(function(){
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('teachers.login')->defaults('type','teacher');
        Route::post('/login', [AuthController::class, 'login'])->middleware('guest')->defaults('type','teacher');
        Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('teachers.register')->defaults('type','teacher');
        Route::post('/register', [AuthController::class, 'register'])->middleware('guest')->defaults('type','teacher');
    });
    Route::prefix('students')->group(function(){
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('students.login')->defaults('type','student');
        Route::post('/login', [AuthController::class, 'login'])->middleware('guest')->defaults('type','student');
        Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('students.register')->defaults('type','student');
        Route::post('/register', [AuthController::class, 'register'])->middleware('guest')->defaults('type','student');
    });
    Route::prefix('parents')->group(function(){
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('parents.login')->defaults('type','parent');
        Route::post('/login', [AuthController::class, 'login'])->middleware('guest')->defaults('type','parent');
        Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('parents.register')->defaults('type','parent');
        Route::post('/register', [AuthController::class, 'register'])->middleware('guest')->defaults('type','parent');
    });

    // Legacy singular auth paths redirect to plural
    Route::prefix('teacher')->group(function(){
        Route::get('/login', fn() => redirect()->route('teachers.login'))->name('teacher.login');
        Route::get('/register', fn() => redirect()->route('teachers.register'))->name('teacher.register');
    });
    Route::prefix('student')->group(function(){
        Route::get('/login', fn() => redirect()->route('students.login'))->name('student.login');
        Route::get('/register', fn() => redirect()->route('students.register'))->name('student.register');
    });
    Route::prefix('parent')->group(function(){
        Route::get('/login', fn() => redirect()->route('parents.login'))->name('parent.login');
        Route::get('/register', fn() => redirect()->route('parents.register'))->name('parent.register');
    });
    Route::prefix('staff')->group(function(){
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('staff.login')->defaults('type','staff');
        Route::post('/login', [AuthController::class, 'login'])->middleware('guest')->defaults('type','staff');
        Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('staff.register')->defaults('type','staff');
        Route::post('/register', [AuthController::class, 'register'])->middleware('guest')->defaults('type','staff');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

    // Generic dashboard remains available
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware('auth')->name('dashboard');

    // Role dashboards nested under base segment
    Route::prefix('admin')->middleware(['auth','role:admin'])->group(function(){
        Route::get('/dashboard', fn() => view('dashboards.admin'))->name('admin.dashboard');
    });
    // Plural role dashboards
    Route::prefix('teachers')->middleware(['auth','role:teacher'])->group(function(){
        Route::get('/dashboard', fn() => view('dashboards.teacher'))->name('teachers.dashboard');
    });
    Route::prefix('students')->middleware(['auth','role:student'])->group(function(){
        Route::get('/dashboard', fn() => view('dashboards.student'))->name('students.dashboard');
    });
    Route::prefix('parents')->middleware(['auth','role:parent'])->group(function(){
        Route::get('/dashboard', fn() => view('dashboards.parent'))->name('parents.dashboard');
    });
    Route::prefix('staff')->middleware(['auth','role:staff'])->group(function(){
        Route::get('/dashboard', fn() => view('dashboards.staff'))->name('staff.dashboard');
    });

    // Legacy singular role dashboards redirect to plural
    Route::prefix('teacher')->middleware(['auth','role:teacher'])->group(function(){
        Route::get('/', fn() => redirect()->route('teachers.dashboard'))->name('base.teacher');
        Route::get('/dashboard', fn() => redirect()->route('teachers.dashboard'))->name('teacher.dashboard');
    });
    Route::prefix('student')->middleware(['auth','role:student'])->group(function(){
        Route::get('/', fn() => redirect()->route('students.dashboard'))->name('base.student');
        Route::get('/dashboard', fn() => redirect()->route('students.dashboard'))->name('student.dashboard');
    });
    Route::prefix('parent')->middleware(['auth','role:parent'])->group(function(){
        Route::get('/', fn() => redirect()->route('parents.dashboard'))->name('base.parent');
        Route::get('/dashboard', fn() => redirect()->route('parents.dashboard'))->name('parent.dashboard');
    });
});

// API-like routes for basic management with RBAC
Route::middleware(['web'])->group(function () {
    // Move admin-only endpoints under /admin/*
    Route::prefix('admin')->middleware(['auth','role:admin'])->group(function(){
        // School profile
        Route::get('/school/profile', [SchoolProfileController::class, 'show'])->name('admin.school.profile');
        Route::post('/school/profile', [SchoolProfileController::class, 'upsert'])->name('admin.school.profile.save');

        // Location AJAX endpoints
        Route::get('/locations/countries', [LocationController::class, 'countries'])->name('admin.locations.countries');
        Route::get('/locations/districts', [LocationController::class, 'districts'])->name('admin.locations.districts');
        Route::get('/locations/upazilas', [LocationController::class, 'upazilas'])->name('admin.locations.upazilas');

        // Holidays management
        Route::get('/school/holidays', [\App\Http\Controllers\HolidayController::class, 'index'])->name('admin.school.holidays.index');
        Route::post('/school/holidays', [\App\Http\Controllers\HolidayController::class, 'store'])->name('admin.school.holidays.store');
        Route::delete('/school/holidays/{holiday}', [\App\Http\Controllers\HolidayController::class, 'destroy'])->name('admin.school.holidays.destroy');
        Route::post('/school/holidays/sync-govt', [\App\Http\Controllers\HolidayController::class, 'syncGovt'])->name('admin.school.holidays.sync');

        // Users management
        Route::get('/users', [UserManagementController::class, 'index']);
        Route::get('/users/pending-section', [UserManagementController::class, 'pending']);
        Route::get('/users/users-section', [UserManagementController::class, 'all']);
        Route::post('/users/{user}/assign-role', [UserManagementController::class, 'assignRole']);
        Route::post('/users/{user}/revoke-role', [UserManagementController::class, 'revokeRole']);
        Route::post('/users/{user}/approve', [UserManagementController::class, 'approve']);
        Route::post('/users/{user}/activate', [UserManagementController::class, 'activate']);
        Route::post('/users/{user}/deactivate', [UserManagementController::class, 'deactivate']);
        Route::put('/users/{user}', [UserManagementController::class, 'update']);
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy']);

        // Notifications
        Route::post('/users/{user}/notify', [NotificationController::class, 'notifyUser']);
        // Management Module (CRUD + bulk upload)
        Route::prefix('management')->name('admin.management.')->group(function(){
            // Countries
            Route::get('/countries', [CountryController::class, 'index'])->name('countries.index');
            Route::get('/countries/create', [CountryController::class, 'create'])->name('countries.create');
            Route::post('/countries', [CountryController::class, 'store'])->name('countries.store');
            Route::get('/countries/{country}/edit', [CountryController::class, 'edit'])->name('countries.edit');
            Route::put('/countries/{country}', [CountryController::class, 'update'])->name('countries.update');
            Route::delete('/countries/{country}', [CountryController::class, 'destroy'])->name('countries.destroy');
            Route::post('/countries/upload', [CountryController::class, 'upload'])->name('countries.upload');

            // Districts
            Route::get('/districts', [DistrictController::class, 'index'])->name('districts.index');
            Route::get('/districts/create', [DistrictController::class, 'create'])->name('districts.create');
            Route::post('/districts', [DistrictController::class, 'store'])->name('districts.store');
            Route::get('/districts/{district}/edit', [DistrictController::class, 'edit'])->name('districts.edit');
            Route::put('/districts/{district}', [DistrictController::class, 'update'])->name('districts.update');
            Route::delete('/districts/{district}', [DistrictController::class, 'destroy'])->name('districts.destroy');
            Route::post('/districts/upload', [DistrictController::class, 'upload'])->name('districts.upload');

            // Divisions
            Route::get('/divisions', [DivisionController::class, 'index'])->name('divisions.index');
            Route::get('/divisions/create', [DivisionController::class, 'create'])->name('divisions.create');
            Route::post('/divisions', [DivisionController::class, 'store'])->name('divisions.store');
            Route::get('/divisions/{division}/edit', [DivisionController::class, 'edit'])->name('divisions.edit');
            Route::put('/divisions/{division}', [DivisionController::class, 'update'])->name('divisions.update');
            Route::delete('/divisions/{division}', [DivisionController::class, 'destroy'])->name('divisions.destroy');
            Route::post('/divisions/upload', [DivisionController::class, 'upload'])->name('divisions.upload');

            // Upazilas
            Route::get('/upazilas', [UpazilaController::class, 'index'])->name('upazilas.index');
            Route::get('/upazilas/create', [UpazilaController::class, 'create'])->name('upazilas.create');
            Route::post('/upazilas', [UpazilaController::class, 'store'])->name('upazilas.store');
            Route::get('/upazilas/{upazila}/edit', [UpazilaController::class, 'edit'])->name('upazilas.edit');
            Route::put('/upazilas/{upazila}', [UpazilaController::class, 'update'])->name('upazilas.update');
            Route::delete('/upazilas/{upazila}', [UpazilaController::class, 'destroy'])->name('upazilas.destroy');
            Route::post('/upazilas/upload', [UpazilaController::class, 'upload'])->name('upazilas.upload');
        });

    });

    // Backwards-compatible redirects (optional, can be removed later)
    Route::get('/school/profile', fn() => redirect('/admin/school/profile')); // GET
    Route::post('/school/profile', fn() => redirect('/admin/school/profile')); // POST

    Route::get('/users', fn() => redirect('/admin/users'));
    Route::get('/users/pending-section', fn() => redirect('/admin/users/pending-section'));
    Route::get('/users/users-section', fn() => redirect('/admin/users/users-section'));
    Route::post('/users/{user}/assign-role', fn($user) => redirect("/admin/users/{$user}/assign-role"));
    Route::post('/users/{user}/revoke-role', fn($user) => redirect("/admin/users/{$user}/revoke-role"));
    Route::post('/users/{user}/approve', fn($user) => redirect("/admin/users/{$user}/approve"));
    Route::post('/users/{user}/activate', fn($user) => redirect("/admin/users/{$user}/activate"));
    Route::post('/users/{user}/deactivate', fn($user) => redirect("/admin/users/{$user}/deactivate"));
    Route::put('/users/{user}', fn($user) => redirect("/admin/users/{$user}"));
    Route::delete('/users/{user}', fn($user) => redirect("/admin/users/{$user}"));
    Route::post('/users/{user}/notify', fn($user) => redirect("/admin/users/{$user}/notify"));

    // Legacy dashboard routes redirect to new nested ones
    Route::get('/dashboard/admin', fn() => redirect()->route('admin.dashboard'));
    Route::get('/dashboard/teacher', fn() => redirect()->route('teachers.dashboard'));
    Route::get('/dashboard/student', fn() => redirect()->route('students.dashboard'));
    Route::get('/dashboard/parent', fn() => redirect()->route('parents.dashboard'));
    Route::get('/dashboard/staff', fn() => redirect()->route('staff.dashboard'));
});
