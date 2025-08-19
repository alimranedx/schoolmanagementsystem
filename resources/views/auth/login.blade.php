@extends('layouts.auth')

@section('content')
@if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

@if (!isset($type) || !$type)
    <div class="card shadow-sm">
        <div class="card-header bg-white"><strong>Select user type to log in</strong></div>
        <div class="card-body">
            <div class="row g-2">
                <div class="col-6 col-md-4"><a class="btn btn-outline-primary w-100" href="{{ route('admin.login') }}">Admin</a></div>
                <div class="col-6 col-md-4"><a class="btn btn-outline-primary w-100" href="{{ route('teachers.login') }}">Teachers</a></div>
                <div class="col-6 col-md-4"><a class="btn btn-outline-primary w-100" href="{{ route('students.login') }}">Students</a></div>
                <div class="col-6 col-md-4"><a class="btn btn-outline-primary w-100" href="{{ route('parents.login') }}">Parents</a></div>
                <div class="col-6 col-md-4"><a class="btn btn-outline-primary w-100" href="{{ route('staff.login') }}">Staff</a></div>
            </div>
            <div class="mt-3 text-center">
                <small>Need an account? Register as:
                    <a href="{{ route('admin.register') }}">Admin</a>,
                    <a href="{{ route('teachers.register') }}">Teachers</a>,
                    <a href="{{ route('students.register') }}">Students</a>,
                    <a href="{{ route('parents.register') }}">Parents</a>,
                    <a href="{{ route('staff.register') }}">Staff</a>
                </small>
            </div>
        </div>
    </div>
@else
    <div class="card shadow-sm">
        <div class="card-header bg-white"><strong>{{ ucfirst($type) }} Login</strong></div>
        <div class="card-body">
            @php
                $actionMap = [
                    'admin' => route('admin.login'),
                    'teacher' => route('teachers.login'),
                    'student' => route('students.login'),
                    'parent' => route('parents.login'),
                    'staff' => route('staff.login'),
                ];
            @endphp
            <form method="POST" action="{{ $actionMap[$type] ?? url('/login') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autofocus>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            <div class="mt-3 text-center">
                @php
                    $registerMap = [
                        'admin' => route('admin.register'),
                        'teacher' => route('teachers.register'),
                        'student' => route('students.register'),
                        'parent' => route('parents.register'),
                        'staff' => route('staff.register'),
                    ];
                @endphp
                <small>Don't have a {{ $type }} account? <a href="{{ $registerMap[$type] ?? route('register') }}">Register</a></small>
            </div>
        </div>
    </div>
@endif
@endsection
