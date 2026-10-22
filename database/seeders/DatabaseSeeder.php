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
        $this->call(AdminUserSeeder::class);
        $this->call(Stage4MutokoContentSeeder::class);
        $this->call(Stage4bGovernanceSeeder::class);
        $this->call(DepartmentSeeder::class);
        $this->call(Stage5TourismSeeder::class);
        $this->call(CapitalProjectSeeder::class);
        $this->call(FinancialDisclosureSeeder::class);
        $this->call(InvestmentProspectusSeeder::class);
        $this->call(VacancySeeder::class);
        $this->call(TenderSeeder::class);
        $this->call(Stage7PrivacySeeder::class);
        $this->call(CouncilTeamSeeder::class);
        $this->call(GallerySeeder::class);
        $this->call(EmergencyNoticeSeeder::class);
        $this->call(BankAccountSeeder::class);
        $this->call(FacebookContentSeeder::class);
        $this->call(CouncilMeetingSeeder::class);
        $this->call(PublishSeededInformationSeeder::class);
    }
}
