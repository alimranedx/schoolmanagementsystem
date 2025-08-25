@extends('layouts.admin')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="mb-0">Upazilas</h4>
    <div>
        <a href="{{ route('admin.management.upazilas.create') }}" class="btn btn-primary"><i class="bi bi-plus"></i> Add Upazila</a>
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
        <form class="row g-2 align-items-end" method="get" action="{{ route('admin.management.upazilas.index') }}">
            <div class="col-sm-6 col-md-3">
                <label class="form-label">Search</label>
                <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Upazila name">
            </div>
            <div class="col-sm-6 col-md-3">
                <label class="form-label">Country</label>
                <select name="country_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}" {{ ($countryId==$country->id)?'selected':'' }}>{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6 col-md-3">
                <label class="form-label">District</label>
                <select name="district_id" class="form-select">
                    <option value="">All</option>
                    @foreach($districts as $district)
                        <option value="{{ $district->id }}" {{ ($districtId==$district->id)?'selected':'' }}>{{ $district->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-primary"><i class="bi bi-search"></i> Filter</button>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.management.upazilas.index') }}" class="btn btn-outline-secondary">Reset</a>
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
        <form method="post" action="{{ route('admin.management.upazilas.upload') }}" enctype="multipart/form-data" class="row g-2 align-items-end">
            @csrf
            <div class="col-sm-8 col-md-5">
                <label class="form-label">CSV file</label>
                <input type="file" name="file" class="form-control" accept=".csv,text/csv">
                <div class="form-text">Headers: name, district (and optional country)</div>
            </div>
            <div class="col-sm-4 col-md-3">
                <label class="form-label">District (optional)</label>
                <select name="district_id" class="form-select">
                    <option value="">Select</option>
                    @foreach($districts as $district)
                        <option value="{{ $district->id }}">{{ $district->name }}</option>
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
                    <th>District</th>
                    <th>Country</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($upazilas as $upazila)
                    <tr>
                        <td>{{ $upazila->id }}</td>
                        <td>{{ $upazila->name }}</td>
                        <td>{{ $upazila->district?->name }}</td>
                        <td>{{ $upazila->district?->country?->name }}</td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.management.upazilas.edit', $upazila) }}"><i class="bi bi-pencil"></i></a>
                            <form method="post" action="{{ route('admin.management.upazilas.destroy', $upazila) }}" class="d-inline" onsubmit="return confirm('Delete this upazila?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">No data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body border-top">
        {{ $upazilas->links() }}
    </div>
</div>

<script>
function downloadSample(){
    const csv = 'name,district,country\nDhanmondi,Dhaka,Bangladesh\nTaltala,Kolkata,India\n';
    const blob = new Blob([csv], {type: 'text/csv'});
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'upazilas_sample.csv';
    a.click();
}
</script>
@endsection
