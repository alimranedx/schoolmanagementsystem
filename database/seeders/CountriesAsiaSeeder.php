<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CountriesAsiaSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now()->toDateTimeString();
        $countries = [
            // ISO2, Name
            ['iso2' => 'AF', 'name' => 'Afghanistan'],
            ['iso2' => 'AM', 'name' => 'Armenia'],
            ['iso2' => 'AZ', 'name' => 'Azerbaijan'],
            ['iso2' => 'BH', 'name' => 'Bahrain'],
            ['iso2' => 'BD', 'name' => 'Bangladesh'],
            ['iso2' => 'BT', 'name' => 'Bhutan'],
            ['iso2' => 'BN', 'name' => 'Brunei'],
            ['iso2' => 'KH', 'name' => 'Cambodia'],
            ['iso2' => 'CN', 'name' => 'China'],
            ['iso2' => 'CY', 'name' => 'Cyprus'],
            ['iso2' => 'GE', 'name' => 'Georgia'],
            ['iso2' => 'IN', 'name' => 'India'],
            ['iso2' => 'ID', 'name' => 'Indonesia'],
            ['iso2' => 'IR', 'name' => 'Iran'],
            ['iso2' => 'IQ', 'name' => 'Iraq'],
            ['iso2' => 'IL', 'name' => 'Israel'],
            ['iso2' => 'JP', 'name' => 'Japan'],
            ['iso2' => 'JO', 'name' => 'Jordan'],
            ['iso2' => 'KZ', 'name' => 'Kazakhstan'],
            ['iso2' => 'KW', 'name' => 'Kuwait'],
            ['iso2' => 'KG', 'name' => 'Kyrgyzstan'],
            ['iso2' => 'LA', 'name' => 'Laos'],
            ['iso2' => 'LB', 'name' => 'Lebanon'],
            ['iso2' => 'MY', 'name' => 'Malaysia'],
            ['iso2' => 'MV', 'name' => 'Maldives'],
            ['iso2' => 'MN', 'name' => 'Mongolia'],
            ['iso2' => 'MM', 'name' => 'Myanmar'],
            ['iso2' => 'NP', 'name' => 'Nepal'],
            ['iso2' => 'KP', 'name' => 'North Korea'],
            ['iso2' => 'OM', 'name' => 'Oman'],
            ['iso2' => 'PK', 'name' => 'Pakistan'],
            ['iso2' => 'PS', 'name' => 'Palestine'],
            ['iso2' => 'PH', 'name' => 'Philippines'],
            ['iso2' => 'QA', 'name' => 'Qatar'],
            ['iso2' => 'SA', 'name' => 'Saudi Arabia'],
            ['iso2' => 'SG', 'name' => 'Singapore'],
            ['iso2' => 'KR', 'name' => 'South Korea'],
            ['iso2' => 'LK', 'name' => 'Sri Lanka'],
            ['iso2' => 'SY', 'name' => 'Syria'],
            ['iso2' => 'TW', 'name' => 'Taiwan'],
            ['iso2' => 'TJ', 'name' => 'Tajikistan'],
            ['iso2' => 'TH', 'name' => 'Thailand'],
            ['iso2' => 'TL', 'name' => 'Timor-Leste'],
            ['iso2' => 'TR', 'name' => 'Turkey'],
            ['iso2' => 'TM', 'name' => 'Turkmenistan'],
            ['iso2' => 'AE', 'name' => 'United Arab Emirates'],
            ['iso2' => 'UZ', 'name' => 'Uzbekistan'],
            ['iso2' => 'VN', 'name' => 'Vietnam'],
            ['iso2' => 'YE', 'name' => 'Yemen'],
        ];

        $rows = array_map(function($c) use ($now) {
            return [
                'name' => $c['name'],
                'iso2' => $c['iso2'],
                'region' => 'Asia',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $countries);

        DB::table('countries')->upsert($rows, ['iso2'], ['name','region','updated_at']);
    }
}
