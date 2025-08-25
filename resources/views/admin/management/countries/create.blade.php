@extends('layouts.admin')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="mb-0">Add Country</h4>
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
        <form method="post" action="{{ route('admin.management.countries.store') }}" class="row g-3">
            @csrf
            <div class="col-md-6">
                <label class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">ISO2</label>
                <input type="text" name="iso2" class="form-control" value="{{ old('iso2') }}" maxlength="2">
            </div>
            <div class="col-md-3">
                <label class="form-label">Region</label>
                <input type="text" name="region" class="form-control" value="{{ old('region') }}">
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection
