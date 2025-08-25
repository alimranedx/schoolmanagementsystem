<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\District;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DistrictController extends Controller
{
    public function index(Request $request): View
    {
        $q = $request->query('q');
        $countryId = $request->query('country_id');
        $districts = District::with('country')
            ->when($q, fn($query) => $query->where('name', 'like', "%$q%"))
            ->when($countryId, fn($query) => $query->where('country_id', $countryId))
            ->orderBy('name')
            ->paginate(15)
            ->appends(['q' => $q, 'country_id' => $countryId]);
        $countries = Country::orderBy('name')->get();
        return view('admin.management.districts.index', compact('districts', 'countries', 'q', 'countryId'));
    }

    public function create(): View
    {
        $countries = Country::orderBy('name')->get();
        return view('admin.management.districts.create', compact('countries'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:190',
            'country_id' => 'required|exists:countries,id',
        ]);
        District::create($data);
        return redirect()->route('admin.management.districts.index')->with('success', 'District created successfully.');
    }

    public function edit(District $district): View
    {
        $countries = Country::orderBy('name')->get();
        return view('admin.management.districts.edit', compact('district','countries'));
    }

    public function update(Request $request, District $district): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:190',
            'country_id' => 'required|exists:countries,id',
        ]);
        $district->update($data);
        return redirect()->route('admin.management.districts.index')->with('success', 'District updated successfully.');
    }

    public function destroy(District $district): RedirectResponse
    {
        $district->delete();
        return back()->with('success', 'District deleted.');
    }

    public function upload(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
            'country_id' => 'nullable|exists:countries,id',
        ]);
        $file = $request->file('file');
        $countryIdDefault = $request->input('country_id');
        $created = 0; $updated = 0; $failed = 0;
        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $header = null;
            while (($row = fgetcsv($handle)) !== false) {
                if ($header === null) { $header = array_map(fn($h) => strtolower(trim($h)), $row); continue; }
                $data = array_combine($header, $row);
                if (!$data) { $failed++; continue; }
                $name = trim($data['name'] ?? '');
                $countryName = trim($data['country'] ?? '');
                $countryId = $countryIdDefault;
                if (!$countryId && $countryName) {
                    $country = Country::where('name', $countryName)->first();
                    $countryId = $country?->id;
                }
                if ($name === '' || !$countryId) { $failed++; continue; }
                $district = District::firstOrNew(['name' => $name, 'country_id' => $countryId]);
                $isNew = !$district->exists;
                $district->save();
                $isNew ? $created++ : $updated++;
            }
            fclose($handle);
        }
        return back()->with('success', "Upload complete. Created: $created, Updated: $updated, Failed: $failed");
    }
}
