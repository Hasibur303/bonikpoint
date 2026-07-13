<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(ProfessionalCategorySeeder::class);
        $this->call(AtvsGenxProductSeeder::class);
        $this->call(SmokVapePen22ProductSeeder::class);
        $this->call(RincoeMantoNanoA4ProductSeeder::class);

        User::updateOrCreate(
            ['email' => 'admin@bonikpoint.com'],
            [
                'name' => 'Bonik Point Admin',
                'mobile' => '01700000000',
                'password' => bcrypt('password'),
                'utype' => 'adm',
            ]
        );

        User::updateOrCreate(
            ['email' => 'customer@bonikpoint.com'],
            [
                'name' => 'Demo Customer',
                'mobile' => '01800000000',
                'password' => bcrypt('password'),
                'utype' => 'usr',
            ]
        );

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'mobile' => '01900000000',
                'password' => bcrypt('password'),
                'utype' => 'usr',
            ]
        );
    }
}
