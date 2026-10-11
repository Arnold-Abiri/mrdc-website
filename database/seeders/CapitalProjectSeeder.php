<?php

namespace Database\Seeders;

use App\Models\CouncilProject;
use Illuminate\Database\Seeder;

class CapitalProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = [
            ['slug' => 'bepeta-rural-health-centre', 'title' => 'Bepeta Rural Health Centre', 'location' => 'Bepeta, Mutoko South', 'summary' => 'Rural health centre constructed with devolution funding.', 'description' => 'The Zimbabwe National Development Strategy 2 identifies Bepeta Rural Health Centre as a completed Mutoko RDC health project funded by the Devolution Fund. Source: https://www.zim.gov.zw/index.php/government-documents/category/3-national-development-plans?download=10%3Ands2', 'project_status' => 'completed'],
            ['slug' => 'chisambiro-clinic', 'title' => 'Chisambiro Clinic', 'location' => 'Ward 18, Mutoko', 'summary' => 'Clinic construction reported as completed in 2023.', 'description' => 'A 2023 report quoting Mutoko RDC listed Chisambiro Clinic in Ward 18 among completed devolution-funded projects. Source: https://www.heraldonline.co.zw/chikomba-rdc-commissions-service-delivery-equipment/', 'project_status' => 'completed'],
            ['slug' => 'chatiza-high-school-laboratories', 'title' => 'Chatiza High School science and computer laboratories', 'location' => 'Ward 14, Mutoko', 'summary' => 'Science and computer laboratory construction reported as completed in 2023.', 'description' => 'A 2023 report quoting Mutoko RDC listed science and computer laboratories at Chatiza High School in Ward 14 among completed devolution-funded projects. Source: https://www.heraldonline.co.zw/chikomba-rdc-commissions-service-delivery-equipment/', 'project_status' => 'completed'],
        ];

        foreach ($projects as $displayOrder => $project) {
            if (CouncilProject::query()->where('slug', $project['slug'])->exists()) {
                continue;
            }

            CouncilProject::query()->create([
                ...$project,
                'project_type' => 'project',
                'display_order' => $displayOrder,
            ]);
        }
    }
}
