<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\District;
use App\Models\Upazila;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function countries(Request $request)
    {
        $region = $request->query('region', 'Asia');
        return response()->json(
            Country::query()->when($region, fn($q) => $q->where('region', $region))->orderBy('name')->get(['id','name','iso2'])
        );
    }

    public function districts(Request $request)
    {
        $countryId = $request->query('country_id');
        if (!$countryId) return response()->json([]);
        return response()->json(
            District::where('country_id', $countryId)->orderBy('name')->get(['id','name'])
        );
    }

    public function upazilas(Request $request)
    {
        $districtId = $request->query('district_id');
        if (!$districtId) return response()->json([]);
        return response()->json(
            Upazila::where('district_id', $districtId)->orderBy('name')->get(['id','name'])
        );
    }
}
