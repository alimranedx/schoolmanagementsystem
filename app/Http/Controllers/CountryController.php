<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CountryController extends Controller
{
    public function index(Request $request): View
    {
        $q = $request->query('q');
        $countries = Country::query()
            ->when($q, fn($query) => $query->where('name', 'like', "%$q%")
                ->orWhere('iso2', 'like', "%$q%")
                ->orWhere('region', 'like', "%$q%"))
            ->orderBy('name')
            ->paginate(15)
            ->appends(['q' => $q]);

        return view('admin.management.countries.index', compact('countries', 'q'));
    }

    public function create(): View
    {
        return view('admin.management.countries.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:190',
            'iso2' => 'nullable|string|max:2',
            'region' => 'nullable|string|max:190',
        ]);
        Country::create($data);
        return redirect()->route('admin.management.countries.index')->with('success', 'Country created successfully.');
    }

    public function edit(Country $country): View
    {
        return view('admin.management.countries.edit', compact('country'));
    }

    public function update(Request $request, Country $country): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:190',
            'iso2' => 'nullable|string|max:2',
            'region' => 'nullable|string|max:190',
        ]);
        $country->update($data);
        return redirect()->route('admin.management.countries.index')->with('success', 'Country updated successfully.');
    }

    public function destroy(Country $country): RedirectResponse
    {
        $country->delete();
        return back()->with('success', 'Country deleted.');
    }

    public function upload(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);
        $file = $request->file('file');
        $created = 0; $updated = 0; $failed = 0;
        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $header = null;
            while (($row = fgetcsv($handle)) !== false) {
                if ($header === null) { $header = array_map(fn($h) => strtolower(trim($h)), $row); continue; }
                $data = array_combine($header, $row);
                if (!$data) { $failed++; continue; }
                $name = trim($data['name'] ?? '');
                if ($name === '') { $failed++; continue; }
                $iso2 = strtoupper(trim($data['iso2'] ?? ''));
                $region = trim($data['region'] ?? '');
                $country = Country::firstOrNew(['name' => $name]);
                $isNew = !$country->exists;
                $country->iso2 = $iso2 ?: $country->iso2;
                $country->region = $region ?: $country->region;
                $country->save();
                $isNew ? $created++ : $updated++;
            }
            fclose($handle);
        }
        return back()->with('success', "Upload complete. Created: $created, Updated: $updated, Failed: $failed");
    }
}
