<?php

namespace App\Http\Controllers;

use App\Models\SchoolProfile;
use Illuminate\Http\Request;

class SchoolProfileController extends Controller
{
    public function show()
    {
        $profile = SchoolProfile::first();
        // Render Blade view for admin
        return view('admin.school.profile', compact('profile'));
    }

    public function upsert(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'logo_path' => 'nullable|string|max:1024',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'academic_year' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:100',
            'timezone' => 'nullable|string|max:100',
        ]);

        $profile = SchoolProfile::first();
        if ($profile) {
            $profile->update($data);
        } else {
            $profile = SchoolProfile::create($data);
        }

        if ($request->wantsJson()) {
            return response()->json($profile, 201);
        }
        return redirect()->route('admin.school.profile')->with('status', 'Profile saved');
    }
}
