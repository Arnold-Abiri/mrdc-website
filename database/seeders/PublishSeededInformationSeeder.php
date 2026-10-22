<?php

namespace Database\Seeders;

use App\Models\Gallery;
use App\Models\InvestmentOpportunity;
use App\Models\Page;
use App\Models\Service;
use Illuminate\Database\Seeder;

class PublishSeededInformationSeeder extends Seeder
{
    public function run(): void
    {
        $publishedAt = now();

        Page::query()->whereIn('slug', [
            'about-mutoko',
            'mandate',
            'vision-mission',
            'organogram',
            'rates-information',
            'tourism-mutoko',
            'privacy-policy',
        ])->update([
            'status' => 'published',
            'verification_status' => 'publishable',
            'published_at' => $publishedAt,
        ]);

        Service::query()->whereIn('slug', [
            'education-services',
            'environmental-protection',
            'roads-and-works',
            'health-services',
            'business-centre-servicing',
            'property-management',
            'recreational-facilities',
            'natural-resources-conservation',
            'welfare-services',
        ])->update([
            'status' => 'published',
            'verification_status' => 'publishable',
            'published_at' => $publishedAt,
        ]);

        InvestmentOpportunity::query()->whereIn('slug', [
            'solar-energy-mutoko',
            'mineral-mining-mutoko',
            'horticulture-mutoko',
            'processing-plants-mutoko',
            'residential-stands-mutoko',
            'granite-cutting-polishing-mutoko',
        ])->update([
            'status' => 'published',
            'verification_status' => 'publishable',
            'verified_at' => $publishedAt,
            'published_at' => $publishedAt,
        ]);

        Gallery::query()->whereIn('slug', [
            'community-life-mutoko',
            'council-service-delivery',
        ])->update([
            'status' => 'published',
            'verification_status' => 'publishable',
            'verified_at' => $publishedAt,
            'published_at' => $publishedAt,
        ]);
    }
}
