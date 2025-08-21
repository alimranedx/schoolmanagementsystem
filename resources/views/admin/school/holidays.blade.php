@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex align-items-center justify-content-between">
            <div>
                <strong>Holidays - {{ $year }}</strong>
                <div class="text-muted small">Country: {{ $country ?: 'N/A' }}</div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.school.profile') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-gear me-1"></i> Profile</a>
                <form method="POST" action="{{ route('admin.school.holidays.sync') }}">
                    @csrf
                    <input type="hidden" name="year" value="{{ $year }}">
                    <button class="btn btn-outline-primary btn-sm"><i class="bi bi-download me-1"></i> Sync Bangladesh Govt Holidays</button>
                </form>
            </div>
        </div>
        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-info">{{ session('status') }}</div>
            @endif
            <form method="POST" action="{{ route('admin.school.holidays.store') }}" class="row g-2 align-items-end mb-3">
                @csrf
                <div class="col-md-3">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Holiday Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g., Sports Day" required>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary w-100" type="submit"><i class="bi bi-plus-lg me-1"></i> Add School Holiday</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th style="width: 140px;">Date</th>
                            <th>Name</th>
                            <th style="width: 120px;">Type</th>
                            <th class="text-end" style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($holidays as $h)
                            <tr>
                                <td>{{ \Illuminate\Support\Carbon::parse($h->date)->format('Y-m-d') }}</td>
                                <td>{{ $h->name }}</td>
                                <td><span class="badge text-bg-{{ $h->type==='govt' ? 'success' : 'secondary' }}">{{ $h->type }}</span></td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('admin.school.holidays.destroy', $h) }}" onsubmit="return confirm('Delete this holiday?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No holidays found for {{ $year }}.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
