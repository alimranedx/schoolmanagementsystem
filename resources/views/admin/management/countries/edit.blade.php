@extends('layouts.admin')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="mb-0">Edit Country</h4>
    <a href="{{ route('admin.management.countries.index') }}" class="btn btn-outline-secondary">Back</a>
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
        <form method="post" action="{{ route('admin.management.countries.update', $country) }}" class="row g-3">
            @csrf
            @method('PUT')
            <div class="col-md-6">
                <label class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $country->name) }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">ISO2</label>
                <input type="text" name="iso2" class="form-control" value="{{ old('iso2', $country->iso2) }}" maxlength="2">
            </div>
            <div class="col-md-3">
                <label class="form-label">Region</label>
                <input type="text" name="region" class="form-control" value="{{ old('region', $country->region) }}">
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
