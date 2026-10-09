<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(SecuritySeeder::class);
        $this->call(Stage4MutokoContentSeeder::class);
        $this->call(Stage4bGovernanceSeeder::class);
        $this->call(DepartmentSeeder::class);
        $this->call(Stage5TourismSeeder::class);
        $this->call(VacancySeeder::class);
        $this->call(Stage7PrivacySeeder::class);
    }
}
