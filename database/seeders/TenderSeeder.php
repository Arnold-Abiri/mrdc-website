<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Tender;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TenderSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::query()->first()?->id;
        $engineering = Department::query()->where('name', 'Engineering and Works')->first();
        $finance = Department::query()->where('name', 'Finance')->first();
        $planning = Department::query()->where('name', 'Planning and Development')->first();

        $contact = 'Sealed bids endorsed with the tender number and name must be deposited in the Council Tender Box at Mutoko RDC Main Reception, Stand 366, before 10:00 AM on the closing date. Bidders must be PRAZ-registered with valid tax clearance.';

        $tenders = [
            [
                'reference' => 'MRDC/ENG/2026/01',
                'slug' => 'routine-road-maintenance-plant-hire',
                'title' => 'Hire of Graders and Tippers for Rural Road Maintenance',
                'category' => 'Works',
                'description' => 'Hire of motorised graders, tippers, and compactors for routine maintenance of district feeder roads. Work is issued in packages as directed by the Engineering and Works department.',
                'opens_at' => Carbon::parse('2026-10-01'),
                'closes_at' => Carbon::now()->addDays(30),
                'lifecycle_status' => 'open',
                'contact_instructions' => $contact,
                'department_id' => $engineering?->id,
                'display_order' => 1,
            ],
            [
                'reference' => 'MRDC/WTR/2026/02',
                'slug' => 'borehole-drilling-and-rehabilitation',
                'title' => 'Borehole Drilling and Rehabilitation in Selected Wards',
                'category' => 'Works',
                'description' => 'Drilling of new boreholes and rehabilitation of existing water points, including pump installation, tank bases, and water quality testing.',
                'opens_at' => Carbon::parse('2026-10-01'),
                'closes_at' => Carbon::now()->addDays(21),
                'lifecycle_status' => 'open',
                'contact_instructions' => $contact,
                'department_id' => $engineering?->id,
                'display_order' => 2,
            ],
            [
                'reference' => 'MRDC/FIN/2026/03',
                'slug' => 'supply-of-stationery-and-computer-consumables',
                'title' => 'Supply and Delivery of Stationery and Computer Consumables',
                'category' => 'Goods',
                'description' => 'Framework supply of office stationery, printer consumables, and cleaning materials to council offices on an as-and-when-required basis.',
                'opens_at' => Carbon::parse('2026-08-01'),
                'closes_at' => Carbon::now()->subDays(10),
                'lifecycle_status' => 'closed',
                'contact_instructions' => $contact,
                'department_id' => $finance?->id,
                'display_order' => 3,
            ],
            [
                'reference' => 'MRDC/PLN/2026/04',
                'slug' => 'servicing-of-residential-stands-survey',
                'title' => 'Topographical Survey for Proposed Residential Stands',
                'category' => 'Consultancy',
                'description' => 'Topographical survey, beacon confirmation, and layout verification for proposed residential stands. Awarded following competitive bidding and evaluation.',
                'opens_at' => Carbon::parse('2026-05-01'),
                'closes_at' => Carbon::parse('2026-06-30'),
                'lifecycle_status' => 'awarded',
                'contact_instructions' => $contact,
                'department_id' => $planning?->id,
                'display_order' => 4,
                'award_status' => 'awarded',
                'awarded_to' => 'Survey Services (Pvt) Ltd',
                'awarded_at' => Carbon::parse('2026-08-15'),
                'award_reference' => 'MRDC/PLN/2026/04/AWD',
            ],
        ];

        foreach ($tenders as $item) {
            Tender::query()->updateOrCreate(
                ['slug' => $item['slug']],
                [
                    ...$item,
                    'status' => 'published',
                    'verification_status' => 'publishable',
                    'published_at' => now(),
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                    'verified_by' => $adminId,
                    'verified_at' => now(),
                ]
            );
        }
    }
}
