@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex align-items-center justify-content-between">
            <div>
                <strong>School Profile</strong>
                <div class="text-muted small">Manage basic information about your school</div>
            </div>
            <a href="{{ route('admin.school.holidays.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-calendar3 me-1"></i> Holidays
            </a>
        </div>
        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
            <form method="POST" action="{{ route('admin.school.profile.save') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">School Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $profile->name ?? '') }}" required>
                        @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Academic Year</label>
                        <input type="text" name="academic_year" class="form-control" placeholder="e.g., 2025-2026" value="{{ old('academic_year', $profile->academic_year ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Country</label>
                        <select name="country" class="form-select">
                            <option value="Bangladesh" {{ (old('country', $profile->country ?? 'Bangladesh')=='Bangladesh')?'selected':'' }}>Bangladesh</option>
                            <option value="">Other/Not set</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Timezone</label>
                        <input type="text" name="timezone" class="form-control" placeholder="Asia/Dhaka" value="{{ old('timezone', $profile->timezone ?? 'Asia/Dhaka') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control" value="{{ old('address', $profile->address ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $profile->phone ?? '') }}">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Logo Path (optional)</label>
                        <input type="text" name="logo_path" class="form-control" value="{{ old('logo_path', $profile->logo_path ?? '') }}">
                        <div class="form-text">Provide a URL or storage path to the logo file.</div>
                    </div>
                </div>
                <div class="mt-3">
                    <button class="btn btn-primary" type="submit">Save Profile</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
