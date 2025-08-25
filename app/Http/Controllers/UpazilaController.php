<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\District;
use App\Models\Upazila;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UpazilaController extends Controller
{
    public function index(Request $request): View
    {
        $q = $request->query('q');
        $countryId = $request->query('country_id');
        $districtId = $request->query('district_id');
        $upazilas = Upazila::with('district.country')
            ->when($q, fn($query) => $query->where('name', 'like', "%$q%"))
            ->when($districtId, fn($query) => $query->where('district_id', $districtId))
            ->when($countryId, function($query) use ($countryId) {
                $query->whereHas('district', fn($q) => $q->where('country_id', $countryId));
            })
            ->orderBy('name')
            ->paginate(15)
            ->appends(['q' => $q, 'country_id' => $countryId, 'district_id' => $districtId]);
        $countries = Country::orderBy('name')->get();
        $districts = $countryId ? District::where('country_id', $countryId)->orderBy('name')->get() : District::orderBy('name')->get();
        return view('admin.management.upazilas.index', compact('upazilas','countries','districts','q','countryId','districtId'));
    }

    public function create(): View
    {
        $countries = Country::orderBy('name')->get();
        $districts = District::orderBy('name')->get();
        return view('admin.management.upazilas.create', compact('countries','districts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:190',
            'district_id' => 'required|exists:districts,id',
        ]);
        Upazila::create($data);
        return redirect()->route('admin.management.upazilas.index')->with('success', 'Upazila created successfully.');
    }

    public function edit(Upazila $upazila): View
    {
        $countries = Country::orderBy('name')->get();
        $districts = District::orderBy('name')->get();
        return view('admin.management.upazilas.edit', compact('upazila','countries','districts'));
    }

    public function update(Request $request, Upazila $upazila): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:190',
            'district_id' => 'required|exists:districts,id',
        ]);
        $upazila->update($data);
        return redirect()->route('admin.management.upazilas.index')->with('success', 'Upazila updated successfully.');
    }

    public function destroy(Upazila $upazila): RedirectResponse
    {
        $upazila->delete();
        return back()->with('success', 'Upazila deleted.');
    }

    public function upload(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
            'district_id' => 'nullable|exists:districts,id',
        ]);
        $file = $request->file('file');
        $districtIdDefault = $request->input('district_id');
        $created = 0; $updated = 0; $failed = 0;
        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $header = null;
            while (($row = fgetcsv($handle)) !== false) {
                if ($header === null) { $header = array_map(fn($h) => strtolower(trim($h)), $row); continue; }
                $data = array_combine($header, $row);
                if (!$data) { $failed++; continue; }
                $name = trim($data['name'] ?? '');
                $districtName = trim($data['district'] ?? '');
                $countryName = trim($data['country'] ?? '');
                $districtId = $districtIdDefault;
                if (!$districtId) {
                    if ($districtName && $countryName) {
                        $country = Country::where('name', $countryName)->first();
                        if ($country) {
                            $district = District::where('name', $districtName)->where('country_id', $country->id)->first();
                            $districtId = $district?->id;
                        }
                    } elseif ($districtName) {
                        $district = District::where('name', $districtName)->first();
                        $districtId = $district?->id;
                    }
                }
                if ($name === '' || !$districtId) { $failed++; continue; }
                $upazila = Upazila::firstOrNew(['name' => $name, 'district_id' => $districtId]);
                $isNew = !$upazila->exists;
                $upazila->save();
                $isNew ? $created++ : $updated++;
            }
            fclose($handle);
        }
        return back()->with('success', "Upload complete. Created: $created, Updated: $updated, Failed: $failed");
    }
}
