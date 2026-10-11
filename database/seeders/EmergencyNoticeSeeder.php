<?php

namespace Database\Seeders;

use App\Models\EditorialItem;
use Illuminate\Database\Seeder;

class EmergencyNoticeSeeder extends Seeder
{
    /**
     * Seed realistic emergency warnings and civil protection notices.
     */
    public function run(): void
    {
        $item = EditorialItem::query()->firstOrNew(['slug' => 'heavy-rain-flash-flood-warning-nyadire']);
        $item->fill([
            'type' => 'notice',
            'title' => 'Civil Protection Flood Advisory: Nyadire River Catchment and Low-Lying Crossings',
            'summary' => 'Department of Civil Protection advisory following persistent rainfall in Wards 7, 12, and 17. Motorists and pedestrians must not cross flooded bridges.',
            'category' => 'Civil Protection',
            'body' => "The Mutoko District Civil Protection Committee (DCPC) in liaison with the Meteorological Services Department (MSD) advises residents and motorists across Mutoko District of elevated river flows and flash flooding risks along the Nyadire and Mudzi River basins.\n\nPublic Safety Directives:\n1. Motorists, cyclists, and pedestrians must not attempt to cross flooded rivers, bridges, or causeways.\n2. School children and livestock must be kept away from riverbanks and fast-flowing drainage channels.\n3. Communities situated along low-lying river valleys should remain alert and move to higher ground where water levels begin to rise.\n\nEmergencies, stranded persons, or infrastructure damage should be reported to the Council Central Reception at +263 771 592 888, the nearest Ward Councillor, or the District Civil Protection standby team.",
            'display_order' => 0,
        ]);
        $item->status = 'published';
        $item->verification_status = 'publishable';
        $item->is_urgent = true;
        $item->published_at = now()->subMinutes(5);
        $item->save();
    }
}
