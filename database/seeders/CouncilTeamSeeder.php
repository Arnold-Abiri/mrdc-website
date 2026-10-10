<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\Official;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CouncilTeamSeeder extends Seeder
{
    public function run(): void
    {
        $team = [
            ['b-tasarira', 'B. Tasarira', 'Chief Executive Officer', 'btasarira.png'],
            ['k-k-chamisa', 'K. K. Chamisa', 'Townboard Administrator', 'kchamisa.png'],
            ['z-nhidza', 'Z. Nhidza', 'E. O. Social Service', 'znhidza.png'],
            ['r-makore', 'R. Makore', 'Engineer', 'rmakore.png'],
            ['t-nyabonde', 'T. Nyabonde', 'E. O. Finance', 'tnyabonde.png'],
            ['d-tshuma', 'D. Tshuma', 'District Town Planner', 'tshuma.png'],
            ['o-katuka', 'O. Katuka', 'E. O. HR & Admin', 'okatuka.png'],
            ['t-k-hambaguzha', 'T. K. Hambaguzha', 'E. O. Internal Audit', 'thamba.png'],
            ['d-t-mutangadura', 'D. T. Mutangadura', 'Procurement Officer', 'tmuta.png'],
        ];

        $disk = Storage::disk(config('cms.media_disk', 'local'));

        foreach ($team as $order => [$slug, $name, $title, $filename]) {
            $source = base_path('database/seeders/assets/officials/'.$filename);
            $storagePath = 'officials/reference-team/'.$filename;
            $contents = file_get_contents($source);
            $dimensions = getimagesize($source);

            if ($contents === false || $dimensions === false || (! $disk->exists($storagePath) && ! $disk->put($storagePath, $contents))) {
                throw new \RuntimeException('Unable to import council team portrait: '.$filename);
            }

            $media = Media::query()->where('storage_path', $storagePath)->first();
            if (! $media) {
                $media = Media::unguarded(fn (): Media => Media::query()->create([
                    'title' => $name.' portrait',
                    'alt_text' => $name,
                    'caption' => 'Portrait from the Mutoko RDC team page (https://www.mutokordc.co.zw/team.html).',
                    'original_filename' => $filename,
                    'storage_path' => $storagePath,
                    'mime_type' => 'image/png',
                    'size' => $disk->size($storagePath),
                    'width' => $dimensions[0],
                    'height' => $dimensions[1],
                    'status' => 'active',
                ]));
            }

            if (! Official::query()->where('slug', $slug)->exists()) {
                Official::unguarded(fn (): Official => Official::query()->create([
                    'slug' => $slug,
                    'name' => $name,
                    'title' => $title,
                    'photo_media_id' => $media->id,
                    'display_order' => $order,
                    'status' => 'published',
                    'verification_status' => 'publishable',
                    'published_at' => now(),
                ]));
            }
        }

        Official::query()
            ->whereIn('slug', ['demo-office-chairperson', 'demo-office-ceo', 'demo-office-clerk'])
            ->whereNull('photo_media_id')
            ->whereIn('name', ['Office of the Council Chairperson', 'Office of the Chief Executive Officer', 'Office of the Council Secretary'])
            ->update(['status' => 'unpublished', 'published_at' => null]);
    }
}
