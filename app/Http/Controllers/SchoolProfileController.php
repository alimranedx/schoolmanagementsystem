<?php

namespace App\Http\Controllers;

use App\Models\SchoolProfile;
use Illuminate\Http\Request;

class SchoolProfileController extends Controller
{
    public function show()
    {
        $profile = SchoolProfile::first();
        return response()->json($profile);
    }

    public function upsert(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'logo_path' => 'nullable|string|max:1024',
            'academic_year' => 'nullable|string|max:255',
            'holidays' => 'nullable|array',
        ]);

        $profile = SchoolProfile::first();
        if ($profile) {
            $profile->update($data);
        } else {
            $profile = SchoolProfile::create($data);
        }

        return response()->json($profile, 201);
    }
}
