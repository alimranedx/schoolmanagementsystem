@extends('layouts.auth')

@section('content')
@if (!isset($type) || !$type)
    <div class="card shadow-sm">
        <div class="card-header bg-white"><strong>Select user type to register</strong></div>
        <div class="card-body">
            <div class="row g-2">
                <div class="col-6 col-md-4"><a class="btn btn-outline-success w-100" href="{{ route('admin.register') }}">Admin</a></div>
                <div class="col-6 col-md-4"><a class="btn btn-outline-success w-100" href="{{ route('teachers.register') }}">Teachers</a></div>
                <div class="col-6 col-md-4"><a class="btn btn-outline-success w-100" href="{{ route('students.register') }}">Students</a></div>
                <div class="col-6 col-md-4"><a class="btn btn-outline-success w-100" href="{{ route('parents.register') }}">Parents</a></div>
                <div class="col-6 col-md-4"><a class="btn btn-outline-success w-100" href="{{ route('staff.register') }}">Staff</a></div>
            </div>
            <p class="mt-3 text-muted small mb-0">Your registration will be reviewed and approved by an administrator.</p>
        </div>
    </div>
@else
    <div class="card shadow-sm">
        <div class="card-header bg-white"><strong>{{ ucfirst($type) }} Registration</strong></div>
        <div class="card-body">
            @php
                $registerMap = [
                    'admin' => route('admin.register'),
                    'teacher' => route('teachers.register'),
                    'student' => route('students.register'),
                    'parent' => route('parents.register'),
                    'staff' => route('staff.register'),
                ];
            @endphp
            <form method="POST" action="{{ $registerMap[$type] ?? url('/register') }}">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                </div>
                <button type="submit" class="btn btn-success w-100">Submit for Approval</button>
            </form>
            <div class="mt-3 text-center">
                <small>Already have an account? <a href="{{ route('login.type', ['type' => $type]) }}">Login</a></small>
            </div>
        </div>
    </div>
@endif
@endsection
