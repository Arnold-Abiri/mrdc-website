<?php

namespace Database\Seeders;

use App\Models\ContentTranslation;
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

            $official = Official::query()->where('slug', $slug)->first();
            if (! $official) {
                Official::unguarded(fn (): Official => Official::query()->create([
                    'slug' => $slug,
                    'name' => $name,
                    'title' => $title,
                    'photo_media_id' => $media->id,
                    'display_order' => $order + 1,
                    'status' => 'published',
                    'verification_status' => 'publishable',
                    'published_at' => now(),
                ]));
            } else {
                $official->update(['display_order' => $order + 1, 'photo_media_id' => $media->id]);
            }
        }

        Official::query()
            ->whereIn('slug', ['demo-office-ceo', 'demo-office-clerk'])
            ->whereNull('photo_media_id')
            ->whereIn('name', ['Office of the Chief Executive Officer', 'Office of the Council Secretary'])
            ->update(['status' => 'unpublished', 'published_at' => null]);

        $this->seedChairperson();
    }

    private function seedChairperson(): void
    {
        $chambersPhoto = base_path('database/seeders/assets/facebook/news/mutoko-council-chambers-session.jpg');
        $storagePath = 'officials/chairperson/alderman-trust-kachidza.jpg';
        $disk = Storage::disk(config('cms.media_disk', 'local'));

        $mediaId = null;
        if (file_exists($chambersPhoto)) {
            $contents = file_get_contents($chambersPhoto);
            $dimensions = getimagesize($chambersPhoto);

            if ($contents !== false && $dimensions !== false) {
                if (! $disk->exists($storagePath)) {
                    $disk->put($storagePath, $contents);
                }

                $media = Media::query()->firstOrNew(['storage_path' => $storagePath]);
                $media->fill([
                    'title' => 'Alderman Trust Kachidza - Council Chairperson',
                    'alt_text' => 'Alderman Trust Kachidza, Council Chairperson of Mutoko Rural District Council',
                    'caption' => 'Council Chairperson Alderman Trust Kachidza presiding over council governance deliberations.',
                    'original_filename' => 'mutoko-council-chambers-session.jpg',
                    'mime_type' => 'image/jpeg',
                    'size' => $disk->size($storagePath),
                    'width' => $dimensions[0],
                    'height' => $dimensions[1],
                    'status' => 'active',
                ]);
                $media->save();
                $mediaId = $media->id;
            }
        }

        $bio = 'Alderman Trust Kachidza is the Council Chairperson of Mutoko Rural District Council. As the elected political head of the local authority under the Rural District Councils Act [Chapter 29:13], he presides over Full Council meetings and spearheads the policy direction, legislative oversight, and devolved community development agenda across all 29 wards of Mutoko District. Under his leadership, Council has championed life-changing infrastructure projects financed through devolution funds and local revenues — including the establishment of Chisambiro Clinic (Ward 18), feeder road rehabilitation, solar-powered community water piped schemes, and sustainable natural resource management with local value addition in black granite mining.';

        $chairperson = Official::query()->firstOrNew(['slug' => 'alderman-trust-kachidza']);
        $chairperson->fill([
            'name' => 'Alderman Trust Kachidza',
            'title' => 'Council Chairperson',
            'biography' => $bio,
            'photo_media_id' => $mediaId,
            'display_order' => 0,
            'status' => 'published',
            'verification_status' => 'publishable',
            'published_at' => now(),
            'verified_at' => now(),
        ]);
        $chairperson->save();

        // Seed Shona and Ndebele translations for Council Chairperson
        $translations = [
            'sn' => [
                'title' => 'Sachigaro weKanzuru',
                'biography' => 'Alderman Trust Kachidza ndivo Sachigaro weKanzuru yeDunhu reMutoko. Sevatungamiri vezvematongerwo enyika vakasarudzwa pasi peMutemo weMakanzuru eMaruwa (RDC Act [Chapter 29:13]), vanotungamira misangano yeKanzuru Yose, kupa gwara remitemo, kutarisa mitemo yematunhu, pamwe nekutungamira chirongwa chebudiriro yeDevolution mumawadhi ose makumi maviri nemapfumbamwe (29) eMutoko. Pasi pehutungamiriri hwavo, Kanzuru yakasimudzira mapurojekiti makuru ebudiriro anobhadharwa nemari yeDevolution pamwe nemitero yemuno — kusanganisira kuvakwa kweChisambiro Clinic (Wadhi 18), kugadzirwa kwemigwagwa yekuenda kuminda, zvirongwa zvemvura yepombi zvinofambiswa nemagetsi ezuva, nekuchengetedzwa kwezviwanikwa zvakaita segirenayiti nhema (black granite) kuitira kuti zvibatsire nharaunda dzinobva zvicherwa izvi.',
            ],
            'nd' => [
                'title' => 'USihlalo woMkhandlu',
                'biography' => 'U-Alderman Trust Kachidza nguSihlalo woMkhandlu weSifunda saseMutoko. Njengenhloko yezombusazwe ekhethiweyo ngaphansi koMthetho woMkhandlu weZabelo (Rural District Councils Act [Chapter 29:13]), ukhokhela imihlangano yoMkhandlu Wonke, alethe iziqondiso zezinqubomgomo, ukulawula komthetho, lohlelo lwentuthuko yeDevolution kuwo wonke amawadi angamatshumi amabili lasitshiyagalolunye (29) eMutoko. Ngaphansi kobuholi bakhe, uMkhandlu ususekele amaphrojekthi amakhulu entuthuko axhaswe ngemali yeDevolution lemali engenayo yendawo — okugoqela ukwakhiwa kweChisambiro Clinic (Wadi 18), ukulungiswa kwemigwaqo, izinhlelo zamanzi empompi asebenza ngamandla elanga, lokugcinwa okuhle kwemithombo yengcebo yemvelo ikakhulu igranite emnyama (black granite) ukwenzela ukuthi kuhlomule imiphakathi yendawo.',
            ],
        ];

        foreach ($translations as $loc => $fields) {
            foreach ($fields as $field => $val) {
                ContentTranslation::updateOrCreate(
                    [
                        'translatable_type' => Official::class,
                        'translatable_id' => $chairperson->id,
                        'locale' => $loc,
                        'field' => $field,
                    ],
                    [
                        'value' => $val,
                        'updated_by' => null,
                    ]
                );
            }
        }

        // Ensure legacy placeholder demo-office-chairperson is unpublished and points cleanly
        Official::query()
            ->where('slug', 'demo-office-chairperson')
            ->update([
                'status' => 'unpublished',
                'published_at' => null,
            ]);
    }
}
