<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Division;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DivisionController extends Controller
{
    public function index(Request $request): View
    {
        $q = $request->query('q');
        $countryId = $request->query('country_id');
        $divisions = Division::with('country')
            ->when($q, fn($query) => $query->where('name', 'like', "%$q%"))
            ->when($countryId, fn($query) => $query->where('country_id', $countryId))
            ->orderBy('name')
            ->paginate(15)
            ->appends(['q' => $q, 'country_id' => $countryId]);
        $countries = Country::orderBy('name')->get();
        return view('admin.management.divisions.index', compact('divisions', 'countries', 'q', 'countryId'));
    }

    public function create(): View
    {
        $countries = Country::orderBy('name')->get();
        return view('admin.management.divisions.create', compact('countries'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:190',
            'country_id' => 'required|exists:countries,id',
        ]);
        Division::create($data);
        return redirect()->route('admin.management.divisions.index')->with('success', 'Division created successfully.');
    }

    public function edit(Division $division): View
    {
        $countries = Country::orderBy('name')->get();
        return view('admin.management.divisions.edit', compact('division','countries'));
    }

    public function update(Request $request, Division $division): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:190',
            'country_id' => 'required|exists:countries,id',
        ]);
        $division->update($data);
        return redirect()->route('admin.management.divisions.index')->with('success', 'Division updated successfully.');
    }

    public function destroy(Division $division): RedirectResponse
    {
        $division->delete();
        return back()->with('success', 'Division deleted.');
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
                $division = Division::firstOrNew(['name' => $name, 'country_id' => $countryId]);
                $isNew = !$division->exists;
                $division->save();
                $isNew ? $created++ : $updated++;
            }
            fclose($handle);
        }
        return back()->with('success', "Upload complete. Created: $created, Updated: $updated, Failed: $failed");
    }
}
