<?php

namespace App\Http\Controllers;

use App\Models\SchoolProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SchoolProfileController extends Controller
{
    public function show()
    {
        $profile = SchoolProfile::first();
        // Return a default structure so UI has stable fields to bind to
        if (!$profile) {
            return response()->json([
                'name' => '',
                'logo_path' => null,
                'banner_image_path' => null,
                'academic_year' => '',
                'address' => '',
                'phone' => '',
                'email' => '',
                'website' => '',
                'about' => '',
                'established_year' => '',
                'holidays' => [],
            ]);
        }
        return response()->json($profile);
    }

    public function upsert(Request $request)
    {
        // For file uploads, we use multipart/form-data (handled by FormData in the UI)
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
            'holidays.*.date' => 'required_with:holidays|date',
            'holidays.*.title' => 'required_with:holidays|string|max:255',
            'logo' => 'nullable|image|max:2048', // up to ~2MB
            'banner_image' => 'nullable|image|max:4096', // up to ~4MB
        ]);

        $profile = SchoolProfile::first();

        // Prepare data to save
        $data = $validated;

        // Handle file uploads
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

        return response()->json($profile, 201);
    }
}
