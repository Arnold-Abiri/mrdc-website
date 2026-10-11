<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\Media;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class FinancialDisclosureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reports = [
            [
                'slug' => 'auditor-general-local-authorities-2024-mutoko-2023',
                'title' => 'Auditor-General local authorities report (2024) — Mutoko RDC 2023',
                'description' => 'Official Auditor-General report on local authorities, including the audit of Mutoko Rural District Council for the year ended 31 December 2023. The Auditor-General issued a qualified opinion. Source: https://auditorgeneral.gov.zw/wp-content/uploads/2025/09/LOCAL-AUTHORITIES-2024.pdf',
                'reference_date' => '2023-12-31',
                'filename' => 'local-authorities-2024.pdf',
                'sha256' => 'ae3943c2a24db639874b1dd9ff445111117fc759da3e341c82ccaa81bf3122b9',
            ],
            [
                'slug' => 'auditor-general-local-authorities-2023-mutoko-2022',
                'title' => 'Auditor-General local authorities report (2023) — Mutoko RDC 2022',
                'description' => 'Official Auditor-General report on local authorities, including the audit of Mutoko Rural District Council for the year ended 31 December 2022. Source: https://auditorgeneral.gov.zw/wp-content/uploads/2025/09/LOCAL-AUTHORITIES-2023.pdf',
                'reference_date' => '2022-12-31',
                'filename' => 'local-authorities-2023.pdf',
                'sha256' => 'c45f4aca891dfa5e8086f4eb1967fe1ca88c4696e41f928f6abc7b5fbaaaef34',
            ],
        ];

        $disk = Storage::disk(config('cms.media_disk', 'local'));

        foreach ($reports as $report) {
            $source = base_path('database/seeders/assets/'.$report['filename']);
            if (! is_file($source) || hash_file('sha256', $source) !== $report['sha256']) {
                throw new RuntimeException('Financial disclosure source PDF is missing or has changed: '.$report['filename']);
            }

            $storagePath = 'financial-disclosures/'.$report['filename'];
            if (! $disk->exists($storagePath) && ! $disk->put($storagePath, file_get_contents($source))) {
                throw new RuntimeException('Unable to store financial disclosure PDF: '.$report['filename']);
            }

            $media = Media::query()->where('storage_path', $storagePath)->first();
            if (! $media) {
                $media = Media::unguarded(fn (): Media => Media::query()->create([
                    'title' => $report['title'],
                    'original_filename' => $report['filename'],
                    'storage_path' => $storagePath,
                    'mime_type' => 'application/pdf',
                    'size' => $disk->size($storagePath),
                    'status' => 'active',
                ]));
            }

            if (Document::query()->where('slug', $report['slug'])->exists()) {
                continue;
            }

            Document::unguarded(fn (): Document => Document::query()->create([
                'slug' => $report['slug'],
                'title' => $report['title'],
                'description' => $report['description'],
                'category' => 'financial_report',
                'reference_date' => $report['reference_date'],
                'media_id' => $media->id,
                'visibility' => 'public',
                'status' => 'published',
                'verification_status' => 'publishable',
                'verified_at' => now(),
                'published_at' => now(),
            ]));
        }
    }
}
