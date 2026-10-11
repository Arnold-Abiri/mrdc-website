<?php

namespace Database\Seeders;

use App\Models\CouncilProject;
use App\Models\ProjectUpdate;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CapitalProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = [
            ['slug' => 'bepeta-rural-health-centre', 'title' => 'Bepeta Rural Health Centre', 'location' => 'Bepeta, Mutoko South', 'summary' => 'Rural health centre constructed with devolution funding.', 'description' => 'The Zimbabwe National Development Strategy 2 identifies Bepeta Rural Health Centre as a completed Mutoko RDC health project funded by the Devolution Fund. Source: https://www.zim.gov.zw/index.php/government-documents/category/3-national-development-plans?download=10%3Ands2', 'project_status' => 'completed', 'project_type' => 'project', 'progress_percent' => 100, 'display_order' => 0],
            ['slug' => 'chisambiro-clinic', 'title' => 'Chisambiro Clinic', 'location' => 'Ward 18, Mutoko', 'summary' => 'Clinic construction reported as completed in 2023.', 'description' => 'A 2023 report quoting Mutoko RDC listed Chisambiro Clinic in Ward 18 among completed devolution-funded projects. Source: https://www.heraldonline.co.zw/chikomba-rdc-commissions-service-delivery-equipment/', 'project_status' => 'completed', 'project_type' => 'project', 'progress_percent' => 100, 'display_order' => 1],
            ['slug' => 'chatiza-high-school-laboratories', 'title' => 'Chatiza High School science and computer laboratories', 'location' => 'Ward 14, Mutoko', 'summary' => 'Science and computer laboratory construction reported as completed in 2023.', 'description' => 'A 2023 report quoting Mutoko RDC listed science and computer laboratories at Chatiza High School in Ward 14 among completed devolution-funded projects. Source: https://www.heraldonline.co.zw/chikomba-rdc-commissions-service-delivery-equipment/', 'project_status' => 'completed', 'project_type' => 'project', 'progress_percent' => 100, 'display_order' => 2],
            ['slug' => 'mutoko-township-roads-gravelling', 'title' => 'Mutoko Township Access Roads Gravelling', 'location' => 'Mutoko Centre, Ward 7', 'summary' => 'Gravelling and drainage works on township access roads under the ERRP-II road rehabilitation programme.', 'description' => 'Ongoing gravelling, grading, and storm-drain clearing on township access roads, implemented in phases under the Emergency Road Rehabilitation Programme with community labour support.', 'project_status' => 'ongoing', 'project_type' => 'project', 'progress_percent' => 60, 'display_order' => 3],
            ['slug' => 'nyamuzizi-piped-water-scheme', 'title' => 'Nyamuzizi Piped Water Scheme Extension', 'location' => 'Ward 11, Mutoko', 'summary' => 'Extension of the piped water scheme to new residential stands and the business centre.', 'description' => 'Ongoing extension of the piped water reticulation network, including pipeline laying, storage tank repairs, and new household connections for the business centre and surrounding stands.', 'project_status' => 'ongoing', 'project_type' => 'project', 'progress_percent' => 45, 'display_order' => 4],
            ['slug' => 'kawere-classroom-block', 'title' => 'Kawere Primary School Classroom Block', 'location' => 'Ward 5, Mutoko', 'summary' => 'Planned double classroom block with ablution facilities funded from devolution allocations.', 'description' => 'Planned construction of a double classroom block with ablution facilities at Kawere Primary School. Procurement of materials is at tender stage with works scheduled to commence once awards are concluded.', 'project_status' => 'planned', 'project_type' => 'project', 'progress_percent' => 0, 'display_order' => 5],
            ['slug' => 'ward-feeders-maintenance-programme', 'title' => 'District Feeder Roads Maintenance Programme', 'location' => 'All 29 wards, Mutoko District', 'summary' => 'Annual district-wide programme for grading and spot gravelling of feeder roads.', 'description' => 'Recurrent district programme covering grading, spot gravelling, and culvert maintenance across feeder roads in all 29 wards, prioritised through ward development committees each financial year.', 'project_status' => 'ongoing', 'project_type' => 'programme', 'progress_percent' => 50, 'display_order' => 6],
        ];

        foreach ($projects as $project) {
            $record = CouncilProject::query()->updateOrCreate(
                ['slug' => $project['slug']],
                [
                    ...$project,
                    'status' => 'published',
                    'verification_status' => 'publishable',
                    'published_at' => now(),
                ]
            );

            foreach ($this->updatesFor($project['slug']) as $order => $update) {
                ProjectUpdate::query()->updateOrCreate(
                    ['council_project_id' => $record->id, 'title' => $update['title']],
                    [
                        ...$update,
                        'council_project_id' => $record->id,
                        'display_order' => $order,
                        'status' => 'published',
                        'published_at' => now(),
                    ]
                );
            }
        }
    }

    /**
     * @return list<array{update_date: Carbon, title: string, summary: string, progress_percent: int}>
     */
    private function updatesFor(string $slug): array
    {
        return match ($slug) {
            'bepeta-rural-health-centre' => [
                ['update_date' => Carbon::parse('2026-06-20'), 'title' => 'Health centre commissioned and handed over', 'summary' => 'Construction completed with furnishing done; the facility was commissioned and opened to the public.', 'progress_percent' => 100],
            ],
            'chisambiro-clinic' => [
                ['update_date' => Carbon::parse('2026-05-15'), 'title' => 'Clinic block completed', 'summary' => 'Main clinic block, ablution facilities, and water connection completed and inspected.', 'progress_percent' => 100],
            ],
            'chatiza-high-school-laboratories' => [
                ['update_date' => Carbon::parse('2026-04-30'), 'title' => 'Laboratories completed and equipped', 'summary' => 'Science and computer laboratories completed, with fittings installed and handed over to the school.', 'progress_percent' => 100],
            ],
            'mutoko-township-roads-gravelling' => [
                ['update_date' => Carbon::parse('2026-08-10'), 'title' => 'Phase 1 gravelling completed', 'summary' => 'First phase of gravelling and drain clearing completed on priority township streets.', 'progress_percent' => 40],
                ['update_date' => Carbon::parse('2026-10-02'), 'title' => 'Phase 2 grading underway', 'summary' => 'Grading of remaining sections underway with compaction and spot gravelling in progress.', 'progress_percent' => 60],
            ],
            'nyamuzizi-piped-water-scheme' => [
                ['update_date' => Carbon::parse('2026-09-12'), 'title' => 'Pipeline trenching in progress', 'summary' => 'Trenching and pipe laying for the extension line commenced, with storage tank repairs ongoing.', 'progress_percent' => 45],
            ],
            'kawere-classroom-block' => [
                ['update_date' => Carbon::parse('2026-10-05'), 'title' => 'Materials procurement at tender stage', 'summary' => 'Bill of quantities finalised and materials procurement advertised; site handover follows award.', 'progress_percent' => 0],
            ],
            'ward-feeders-maintenance-programme' => [
                ['update_date' => Carbon::parse('2026-07-25'), 'title' => 'Mid-year grading round completed', 'summary' => 'Scheduled grading completed on priority feeder routes across wards, with culvert repairs ongoing.', 'progress_percent' => 50],
            ],
            default => [],
        };
    }
}
