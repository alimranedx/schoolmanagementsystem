<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class BangladeshGeographySeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now()->toDateTimeString();

        // Ensure Bangladesh exists and get its id
        $countryId = DB::table('countries')->where('iso2', 'BD')->value('id');
        if (!$countryId) {
            $countryId = DB::table('countries')->insertGetId([
                'name' => 'Bangladesh',
                'iso2' => 'BD',
                'region' => 'Asia',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 64 districts of Bangladesh
        $districts = [
            'Bagerhat','Bandarban','Barguna','Barishal','Bhola','Bogura','Brahmanbaria','Chandpur','Chapai Nawabganj','Chattogram','Chuadanga','Cox\'s Bazar','Cumilla','Dhaka','Dinajpur','Faridpur','Feni','Gaibandha','Gazipur','Gopalganj','Habiganj','Jamalpur','Jashore','Jhalokathi','Jhenaidah','Joypurhat','Khagrachhari','Khulna','Kishoreganj','Kurigram','Kushtia','Lakshmipur','Lalmonirhat','Madaripur','Magura','Manikganj','Meherpur','Moulvibazar','Munshiganj','Mymensingh','Naogaon','Narail','Narayanganj','Narsingdi','Natore','Netrokona','Nilphamari','Noakhali','Pabna','Panchagarh','Patuakhali','Pirojpur','Rajbari','Rajshahi','Rangamati','Rangpur','Satkhira','Shariatpur','Sherpur','Sirajganj','Sunamganj','Sylhet','Tangail','Thakurgaon'
        ];

        $districtMap = [];
        foreach ($districts as $name) {
            $id = DB::table('districts')->updateOrInsert(
                ['country_id' => $countryId, 'name' => $name],
                ['updated_at' => $now, 'created_at' => DB::raw('COALESCE(created_at, NOW())')]
            );
        }

        // Fetch ids for created/updated districts
        $districtRows = DB::table('districts')->where('country_id', $countryId)->pluck('id', 'name');

        // Upazilas (partial but real data for representative coverage). Extend as needed.
        $upazilas = [
            'Dhaka' => ['Dhamrai','Dohar','Keraniganj','Nawabganj','Savar'],
            'Gazipur' => ['Gazipur Sadar','Kaliakair','Kaliganj','Kapasia','Sreepur'],
            'Narayanganj' => ['Araihazar','Bandar','Narayanganj Sadar','Rupganj','Sonargaon'],
            'Narsingdi' => ['Belabo','Monohardi','Narsingdi Sadar','Palash','Raipura','Shibpur'],
            'Tangail' => ['Basail','Bhuapur','Delduar','Dhanbari','Ghatail','Gopalpur','Kalihati','Madhupur','Mirzapur','Nagarpur','Sakhipur','Tangail Sadar'],
            'Chattogram' => ['Anwara','Banshkhali','Boalkhali','Chandanaish','Fatikchhari','Hathazari','Lohagara','Mirsharai','Patiya','Rangunia','Raozan','Sandwip','Satkania','Sitakunda'],
            'Cumilla' => ['Barura','Brahmanpara','Burichong','Chandina','Chauddagram','Daudkandi','Debidwar','Homna','Laksam','Meghna','Monohorgonj','Muradnagar','Nangalkot','Cumilla Adarsha Sadar','Cumilla Sadar Dakshin','Titas'],
            'Noakhali' => ['Begumganj','Chatkhil','Companiganj','Hatiya','Kobirhat','Senbagh','Sonaimuri','Subarnachar','Noakhali Sadar'],
            'Rajshahi' => ['Bagha','Bagmara','Charghat','Durgapur','Godagari','Mohanpur','Paba','Puthia','Tanore'],
            'Sylhet' => ['Balaganj','Beanibazar','Bishwanath','Companiganj','Fenchuganj','Fenchugonj','Golapganj','Gowainghat','Jaintiapur','Kanaighat','Osmani Nagar','Sylhet Sadar'],
            'Mymensingh' => ['Bhaluka','Dhobaura','Fulbaria','Gaffargaon','Gauripur','Haluaghat','Ishwarganj','Muktagacha','Mymensingh Sadar','Nandail','Phulpur','Trishal'],
            'Barishal' => ['Agailjhara','Babuganj','Banaripara','Bakerganj','Gournadi','Hizla','Mehendiganj','Muladi','Wazirpur','Barishal Sadar'],
            'Khulna' => ['Batiaghata','Dacope','Daulatpur','Dighalia','Dumuria','Koyra','Paikgachha','Phultala','Rupsha','Terokhada'],
            'Rangpur' => ['Badarganj','Gangachara','Kaunia','Mithapukur','Pirgacha','Pirganj','Rangpur Sadar','Taraganj'],
        ];

        foreach ($upazilas as $districtName => $list) {
            $districtId = $districtRows[$districtName] ?? null;
            if (!$districtId) continue;
            foreach ($list as $name) {
                DB::table('upazilas')->updateOrInsert(
                    ['district_id' => $districtId, 'name' => $name],
                    ['updated_at' => $now, 'created_at' => DB::raw('COALESCE(created_at, NOW())')]
                );
            }
        }
    }
}
