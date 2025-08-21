<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use App\Models\SchoolProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class HolidayController extends Controller
{
    public function index(Request $request)
    {
        $year = (int)($request->query('year') ?: date('Y'));
        $profile = SchoolProfile::first();
        $country = $profile->country ?? 'Bangladesh';
        $holidays = Holiday::where('year', $year)
            ->when($country, fn($q)=>$q->where(function($qq) use ($country){
                $qq->whereNull('country')->orWhere('country', $country);
            }))
            ->orderBy('date')
            ->get();
        return view('admin.school.holidays', compact('holidays','year','country','profile'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'name' => 'required|string|max:255',
        ]);
        $year = (int)date('Y', strtotime($data['date']));
        $profile = SchoolProfile::first();
        $country = $profile->country ?? 'Bangladesh';
        Holiday::firstOrCreate(
            ['date' => $data['date'], 'name' => $data['name'], 'country' => $country],
            ['type' => 'school', 'year' => $year]
        );
        return redirect()->route('admin.school.holidays.index', ['year' => $year])->with('status', 'Holiday saved');
    }

    public function destroy(Holiday $holiday)
    {
        $year = $holiday->year;
        $holiday->delete();
        return redirect()->route('admin.school.holidays.index', ['year' => $year])->with('status', 'Holiday removed');
    }

    public function syncGovt(Request $request)
    {
        $year = (int)($request->input('year') ?: date('Y'));
        $profile = SchoolProfile::first();
        $country = $profile->country ?? 'Bangladesh';
        if (strcasecmp($country, 'Bangladesh') !== 0) {
            return redirect()->back()->with('status', 'Govt holiday sync currently supports Bangladesh only.');
        }
        $list = $this->bangladeshGovtHolidays($year);
        foreach ($list as $h) {
            Holiday::firstOrCreate(
                ['date' => $h['date'], 'name' => $h['name'], 'country' => 'Bangladesh'],
                ['type' => 'govt', 'year' => $year]
            );
        }
        return redirect()->route('admin.school.holidays.index', ['year' => $year])->with('status', 'Bangladesh government holidays synced.');
    }

    private function bangladeshGovtHolidays(int $year): array
    {
        // Note: This includes fixed-date public holidays. Lunar/variable holidays are omitted for simplicity.
        $fixed = [
            ['m' => 2, 'd' => 21, 'name' => 'International Mother Language Day'],
            ['m' => 3, 'd' => 26, 'name' => 'Independence Day'],
            ['m' => 4, 'd' => 14, 'name' => 'Pohela Boishakh (Bengali New Year)'],
            ['m' => 5, 'd' => 1,  'name' => 'May Day'],
            ['m' => 8, 'd' => 15, 'name' => 'National Mourning Day'],
            ['m' => 12,'d' => 16, 'name' => 'Victory Day'],
        ];
        $res = [];
        foreach ($fixed as $f) {
            $date = sprintf('%04d-%02d-%02d', $year, $f['m'], $f['d']);
            $res[] = ['date' => $date, 'name' => $f['name']];
        }
        return $res;
    }
}
