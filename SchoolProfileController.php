<?php

namespace App\Http\Controllers;

use App\Models\SchoolProfile;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SchoolProfileController extends Controller
{
    public function show()
    {
        $profile = SchoolProfile::first();
        $countries = Country::where('region', 'Asia')->orderBy('name')->get();
        return view('admin.school.profile', compact('profile','countries'));
    }

    public function upsert(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'academic_year' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'about' => 'nullable|string',
            'established_year' => 'nullable|integer|min:1800|max:'.(date('Y') + 1),
            'holidays' => 'nullable|array',
            'timezone' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'upazila' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'banner_image' => 'nullable|image|max:4096',
        ]);

        $profile = SchoolProfile::first();

        $data = $validated;

        // Handle file uploads (optional for this form)
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('public/school');
            $data['logo_path'] = Storage::url($path);
        }
        if ($request->hasFile('banner_image')) {
            $path = $request->file('banner_image')->store('public/school');
            $data['banner_image_path'] = Storage::url($path);
        }

        if ($profile) {
            $profile->update($data);
        } else {
            $profile = SchoolProfile::create($data);
        }

        return redirect()->route('admin.school.profile')->with('status', 'School profile saved successfully.');
    }
}
