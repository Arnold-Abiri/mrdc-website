<?php

namespace Database\Seeders;

use App\Models\CouncilMeeting;
use Illuminate\Database\Seeder;

class CouncilMeetingSeeder extends Seeder
{
    public function run(): void
    {
        $meetings = [
            [
                'title' => 'Ordinary Full Council Sitting',
                'meeting_type' => 'full_council',
                'scheduled_date' => now()->addDays(21)->toDateString(),
                'scheduled_time' => '09:00',
                'venue' => 'Council Chambers, Mutoko Centre',
                'meeting_status' => 'scheduled',
                'summary' => 'Quarterly sitting of full council to consider committee reports and resolutions.',
            ],
            [
                'title' => 'Finance and Administration Committee',
                'meeting_type' => 'committee',
                'scheduled_date' => now()->addDays(14)->toDateString(),
                'scheduled_time' => '10:00',
                'venue' => 'Council Chambers, Mutoko Centre',
                'meeting_status' => 'scheduled',
                'summary' => 'Committee sitting on finance, administration and staffing matters.',
            ],
        ];

        foreach ($meetings as $order => $meeting) {
            CouncilMeeting::query()->updateOrCreate(
                ['title' => $meeting['title']],
                [
                    ...$meeting,
                    'status' => 'published',
                    'verification_status' => 'publishable',
                    'published_at' => now(),
                    'verified_at' => now(),
                    'display_order' => $order,
                ],
            );
        }
    }
}
