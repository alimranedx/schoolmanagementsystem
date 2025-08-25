@extends('layouts.admin')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="mb-0">Edit Upazila</h4>
    <a href="{{ route('admin.management.upazilas.index') }}" class="btn btn-outline-secondary">Back</a>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card">
    <div class="card-body">
        <form method="post" action="{{ route('admin.management.upazilas.update', $upazila) }}" class="row g-3">
            @csrf
            @method('PUT')
            <div class="col-md-6">
                <label class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $upazila->name) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">District <span class="text-danger">*</span></label>
                <select name="district_id" class="form-select" required>
                    @foreach($districts as $district)
                        <option value="{{ $district->id }}" {{ (old('district_id', $upazila->district_id)==$district->id) ? 'selected' : '' }}>{{ $district->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
