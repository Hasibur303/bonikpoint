<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
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

        if ($password = env('BONIKPOINT_ADMIN_PASSWORD')) {
            User::updateOrCreate(
                ['email' => env('BONIKPOINT_ADMIN_EMAIL', 'admin@bonikpoint.com')],
                [
                    'name' => 'Bonik Point Admin',
                    'mobile' => env('BONIKPOINT_ADMIN_MOBILE', '01700000000'),
                    'password' => Hash::make($password),
                    'utype' => 'adm',
                ]
            );
        }

        if (app()->environment('production')) {
            return;
        }

        User::updateOrCreate(
            ['email' => 'customer@bonikpoint.com'],
            [
                'name' => 'Demo Customer',
                'mobile' => '01800000000',
                'password' => Hash::make('password'),
                'utype' => 'usr',
            ]
        );

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'mobile' => '01900000000',
                'password' => Hash::make('password'),
                'utype' => 'usr',
            ]
        );
    }
}
