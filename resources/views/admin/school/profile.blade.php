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
                        <label class="form-label">Timezone</label>
                        <input type="text" name="timezone" class="form-control" placeholder="Asia/Dhaka" value="{{ old('timezone', $profile->timezone ?? 'Asia/Dhaka') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Country</label>
                        <select id="country" name="country" class="form-select" required>
                            <option value="">Select country</option>
                            @foreach(($countries ?? []) as $c)
                                <option value="{{ $c->name }}" data-id="{{ $c->id }}" {{ old('country', $profile->country ?? 'Bangladesh') == $c->name ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                        @error('country')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">District</label>
                        <select id="district" name="district" class="form-select">
                            <option value="">Select district</option>
                        </select>
                        @error('district')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Upazila</label>
                        <select id="upazila" name="upazila" class="form-select">
                            <option value="">Select upazila</option>
                        </select>
                        @error('upazila')<div class="text-danger small">{{ $message }}</div>@enderror
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

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    $(function() {
        const routes = {
            districts: "{{ route('admin.locations.districts') }}",
            upazilas: "{{ route('admin.locations.upazilas') }}"
        };

        function getSelectedCountryId() {
            return $('#country option:selected').data('id') || null;
        }

        function loadDistricts(countryId, selectedName) {
            $('#district').html('<option value="">Loading...</option>');
            $('#upazila').html('<option value="">Select upazila</option>');
            if (!countryId) { $('#district').html('<option value="">Select district</option>'); return; }
            $.get(routes.districts, { country_id: countryId })
                .done(function(list){
                    let options = '<option value="">Select district</option>';
                    list.forEach(function(item){
                        const sel = (selectedName && selectedName === item.name) ? ' selected' : '';
                        options += `<option value="${item.name}" data-id="${item.id}"${sel}>${item.name}</option>`;
                    });
                    $('#district').html(options);
                    if (selectedName) {
                        $('#district').trigger('change');
                    }
                })
                .fail(function(){ $('#district').html('<option value="">Select district</option>'); });
        }

        function loadUpazilas(districtId, selectedName) {
            $('#upazila').html('<option value="">Loading...</option>');
            if (!districtId) { $('#upazila').html('<option value="">Select upazila</option>'); return; }
            $.get(routes.upazilas, { district_id: districtId })
                .done(function(list){
                    let options = '<option value="">Select upazila</option>';
                    list.forEach(function(item){
                        const sel = (selectedName && selectedName === item.name) ? ' selected' : '';
                        options += `<option value="${item.name}" data-id="${item.id}"${sel}>${item.name}</option>`;
                    });
                    $('#upazila').html(options);
                })
                .fail(function(){ $('#upazila').html('<option value="">Select upazila</option>'); });
        }

        // Change handlers
        $('#country').on('change', function(){
            const cid = getSelectedCountryId();
            loadDistricts(cid, null);
        });
        $('#district').on('change', function(){
            const did = $('#district option:selected').data('id') || null;
            loadUpazilas(did, null);
        });

        // Initial preselection using existing profile/old values
        const preset = {
            country: `{{ old('country', $profile->country ?? 'Bangladesh') }}`,
            district: `{{ old('district', $profile->district ?? '') }}`,
            upazila: `{{ old('upazila', $profile->upazila ?? '') }}`
        };

        // If country is already selected in the markup, load districts and upazilas accordingly
        const initialCountryId = getSelectedCountryId();
        if (initialCountryId) {
            loadDistricts(initialCountryId, preset.district);
            // Load upazilas after a short delay to ensure district loaded & selected
            const checkDistrictLoaded = setInterval(function(){
                const selectedDistrictId = $('#district option:selected').data('id');
                if (selectedDistrictId || !preset.district) {
                    clearInterval(checkDistrictLoaded);
                    loadUpazilas(selectedDistrictId || null, preset.upazila);
                }
            }, 150);
        }
    });
</script>
@endsection
