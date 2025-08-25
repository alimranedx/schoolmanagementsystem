@extends('layouts.admin')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="mb-0">Edit District</h4>
    <a href="{{ route('admin.management.districts.index') }}" class="btn btn-outline-secondary">Back</a>
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
        <form method="post" action="{{ route('admin.management.districts.update', $district) }}" class="row g-3">
            @csrf
            @method('PUT')
            <div class="col-md-6">
                <label class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $district->name) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Country <span class="text-danger">*</span></label>
                <select name="country_id" class="form-select" required>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}" {{ (old('country_id', $district->country_id)==$country->id) ? 'selected' : '' }}>{{ $country->name }}</option>
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
