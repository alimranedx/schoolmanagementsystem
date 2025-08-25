@extends('layouts.admin')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="mb-0">Districts</h4>
    <div>
        <a href="{{ route('admin.management.districts.create') }}" class="btn btn-primary"><i class="bi bi-plus"></i> Add District</a>
    </div>
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
        <form class="row g-2 align-items-end" method="get" action="{{ route('admin.management.districts.index') }}">
            <div class="col-sm-6 col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="District name">
            </div>
            <div class="col-sm-6 col-md-4">
                <label class="form-label">Country</label>
                <select name="country_id" class="form-select">
                    <option value="">All</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}" {{ ($countryId==$country->id)?'selected':'' }}>{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-primary"><i class="bi bi-search"></i> Filter</button>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.management.districts.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="fw-semibold">Bulk Upload (CSV)</div>
        <a href="#" onclick="downloadSample(); return false;" class="small">Download sample</a>
    </div>
    <div class="card-body">
        <form method="post" action="{{ route('admin.management.districts.upload') }}" enctype="multipart/form-data" class="row g-2 align-items-end">
            @csrf
            <div class="col-sm-8 col-md-6">
                <label class="form-label">CSV file</label>
                <input type="file" name="file" class="form-control" accept=".csv,text/csv">
                <div class="form-text">Headers: name, country (optional if selecting below)</div>
            </div>
            <div class="col-sm-4 col-md-3">
                <label class="form-label">Country (optional)</label>
                <select name="country_id" class="form-select">
                    <option value="">Select</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-success"><i class="bi bi-upload"></i> Upload</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Country</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($districts as $district)
                    <tr>
                        <td>{{ $district->id }}</td>
                        <td>{{ $district->name }}</td>
                        <td>{{ $district->country?->name }}</td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.management.districts.edit', $district) }}"><i class="bi bi-pencil"></i></a>
                            <form method="post" action="{{ route('admin.management.districts.destroy', $district) }}" class="d-inline" onsubmit="return confirm('Delete this district?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted">No data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body border-top">
        {{ $districts->links() }}
    </div>
</div>

<script>
function downloadSample(){
    const csv = 'name,country\nDhaka,Bangladesh\nChennai,India\n';
    const blob = new Blob([csv], {type: 'text/csv'});
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'districts_sample.csv';
    a.click();
}
</script>
@endsection
