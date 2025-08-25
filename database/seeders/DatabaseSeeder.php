<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(CountriesAsiaSeeder::class);
        $this->call(BangladeshGeographySeeder::class);

        // Create an initial admin user (password: password)
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password',
            'status' => 'approved',
            'is_active' => true,
        ]);

        $admin->roles()->sync([\App\Models\Role::where('name', 'admin')->first()->id]);
    }
}
