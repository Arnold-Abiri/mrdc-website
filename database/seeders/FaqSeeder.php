<?php

namespace Database\Seeders;

use App\Models\ContentTranslation;
use App\Models\Department;
use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $financeDept = Department::where('name', 'Finance')->orWhere('code', 'FIN')->first();
        $housingDept = Department::where('name', 'Housing & Community Services')->orWhere('code', 'HCS')->first();
        $engDept = Department::where('name', 'Engineering & Technical Services')->orWhere('code', 'ENG')->first();
        $adminDept = Department::where('name', 'Administration & Human Resources')->orWhere('code', 'ADMIN')->first();

        $faqs = [
            // Billing FAQs (from existing Rates page + localization.ts)
            [
                'question' => 'What payment reference MUST I quote when depositing rates or water bills?',
                'answer' => 'Always quote your Council Assessment/Account Number or your Stand Number and Ward. Without an exact reference, your payment will be held in our unallocated suspense account until manual verification occurs.',
                'category' => 'billing',
                'department_id' => $financeDept?->id,
                'display_order' => 1,
                'is_published' => true,
                'is_featured' => true,
                'translations' => [
                    'sn' => [
                        'question' => 'Ndeipi reference yandinofanira kunyora pandinobhadhara?',
                        'answer' => 'Gara munyora Stand neWadhi renyu. Kana pasina nhamba chaiyo, mari yenyu inosara yakamirira kusvika yaongororwa.',
                    ],
                ],
            ],
            [
                'question' => 'How are payments calculated between Zimbabwe Gold (ZiG) and US Dollars (USD)?',
                'answer' => 'All council tariffs are gazetted in USD baseline and can be legally discharged in ZiG at the ruling RBZ wholesale interbank rate prevailing on the date of banking.',
                'category' => 'billing',
                'department_id' => $financeDept?->id,
                'display_order' => 2,
                'is_published' => true,
                'is_featured' => true,
                'translations' => [
                    'sn' => [
                        'question' => 'Mari inoverengwa sei pakati peZiG neUSD?',
                        'answer' => 'Mitero yakanyorwa muUSD asi inobhadharwa muZiG nemutengo weRBZ wezuva racho.',
                    ],
                ],
            ],
            [
                'question' => 'How do I obtain a Rates Clearance Certificate for property transfer?',
                'answer' => 'Rates Clearance Certificates are issued under Section 138 of the RDC Act. All outstanding rates must be paid in full up to the end of the current quarter. Submit an application to the Revenue Office at Stand 366 Mutoko Centre.',
                'category' => 'billing',
                'department_id' => $financeDept?->id,
                'display_order' => 3,
                'is_published' => true,
                'is_featured' => false,
                'translations' => [
                    'sn' => [
                        'question' => 'Ndingawana sei Rates Clearance Certificate?',
                        'answer' => 'Bhadharai zvikwereti zvose zvemutero moenda kuRevenue Office paStand 366 Mutoko Centre kunoisa chikumbiro.',
                    ],
                ],
            ],
            [
                'question' => 'How quickly is my bank transfer or mobile money payment credited to my account?',
                'answer' => 'When you email your Proof of Payment to the Finance Desk (finance@mutokordc.co.zw), accounts are reconciled and updated within 24 to 48 business hours.',
                'category' => 'billing',
                'department_id' => $financeDept?->id,
                'display_order' => 4,
                'is_published' => true,
                'is_featured' => false,
                'translations' => [
                    'sn' => [
                        'question' => 'Zvinotora nguva yakareba sei kuti mari yebhangi ionekwe muaccount mangu?',
                        'answer' => 'Kana matumira Proof of Payment kuFinance Desk, account renyu rinovandudzwa mukati maawa makumi maviri nemana kusvika makumi mana nemasere (24-48 business hours).',
                    ],
                ],
            ],
            [
                'question' => 'Does Council charge interest on overdue rate accounts?',
                'answer' => 'Yes. Accounts in arrears beyond 30 days attract statutory interest compounded monthly. Ratepayers experiencing hardship are encouraged to enter into payment plans with the Council Treasurer.',
                'category' => 'billing',
                'department_id' => $financeDept?->id,
                'display_order' => 5,
                'is_published' => true,
                'is_featured' => false,
                'translations' => [
                    'sn' => [
                        'question' => 'Kanzuru inobhadharisa chimbadzo pane zvikwereti zvemutero here?',
                        'answer' => 'Hongu. Maaccount anopfuura mazuva makumi matatu asina kubhadharwa anowedzerwa chimbadzo pamwedzi. Vane matambudziko vanokurudzirwa kuuya kuzogadzira hurongwa hwekubhadhara (payment plan) neTreasurer.',
                    ],
                ],
            ],

            // Licences FAQs
            [
                'question' => 'How do I apply for a shop or business operating licence in Mutoko?',
                'answer' => 'Business licence applications require a completed Shop Licence Application Form, certified national ID, certified certificate of incorporation (for registered companies), physical premises lease agreement or proof of ownership, and an Environmental Health inspection clearance certificate.',
                'category' => 'licences',
                'department_id' => $housingDept?->id,
                'display_order' => 1,
                'is_published' => true,
                'is_featured' => true,
                'translations' => [
                    'sn' => [
                        'question' => 'Ndoita sei kuti ndinyorere rezinesi rebhizinesi muMutoko?',
                        'answer' => 'Zvinoda fomu rechikumbiro rezinesi rechitoro, ID, magwaro ekambani, gwaro renzvimbo, pamwe chete neongororo yehutano (Environmental Health clearance).',
                    ],
                ],
            ],
            [
                'question' => 'When is the deadline for annual business licence renewals?',
                'answer' => 'Business licences expire on 31 December of each calendar year. Renewals without penalty are permitted up to 31 January of the following year. Late renewals attract a statutory 50% penalty surcharge.',
                'category' => 'licences',
                'department_id' => $financeDept?->id,
                'display_order' => 2,
                'is_published' => true,
                'is_featured' => false,
                'translations' => [
                    'sn' => [
                        'question' => 'Ndeupi musi wekupedzisira wekuvandudza marezinesi ebhizinesi gore negore?',
                        'answer' => 'Marezinesi ebhizinesi anopera musi wa 31 Zvita (December). Kuvandudza pasina faindi kunotenderwa kusvika 31 Ndira (January). Mushure maizvozvo pane faindi ye 50%.',
                    ],
                ],
            ],

            // Land & Housing FAQs
            [
                'question' => 'What is the procedure for purchasing or being allocated a residential or commercial stand?',
                'answer' => 'Council advertises available stands in national newspapers, at Ward Offices, and on this website. Applicants must register on the Council Housing Waiting List, pay the prescribed registration fee, and submit certified personal and financial documents.',
                'category' => 'land_housing',
                'department_id' => $housingDept?->id,
                'display_order' => 1,
                'is_published' => true,
                'is_featured' => true,
                'translations' => [
                    'sn' => [
                        'question' => 'Ndeipi nzira yekutenga kana kupihwa stand yekugara kana yebhizinesi?',
                        'answer' => 'Kanzuru inoshambadza mastand anenge aripo. Vanoda vanofanira kunyoresa paHousing Waiting List yeKanzuru vobhadhara mari yekunyoresa.',
                    ],
                ],
            ],
            [
                'question' => 'Do I need Council approval before constructing a building or extension?',
                'answer' => 'Yes. Under the Rural District Councils Act and Model Building By-laws, all building plans must be prepared by a qualified draughtsperson and approved by the Council Engineering & Technical Services Department before any construction or pegging begins.',
                'category' => 'land_housing',
                'department_id' => $engDept?->id,
                'display_order' => 2,
                'is_published' => true,
                'is_featured' => false,
                'translations' => [
                    'sn' => [
                        'question' => 'Ndinoda mvumo yeKanzuru here ndisati ndatanga kuvaka chivakwa?',
                        'answer' => 'Hongu. Mapurani ose ekuvaka anofanira kutanga atenderwa neDhipatimendi reEngineering neTechnical Services reKanzuru musati matanga kuvaka.',
                    ],
                ],
            ],

            // Health & Environment FAQs
            [
                'question' => 'How do I report a water leak, burst pipe, or borehole breakdown?',
                'answer' => 'Water faults should be reported immediately to the Council Technical Helpdesk via telephone (+263 77 123 4567), email (technical@mutokordc.co.zw), or by reporting to your local Ward Councillor.',
                'category' => 'health_water',
                'department_id' => $engDept?->id,
                'display_order' => 1,
                'is_published' => true,
                'is_featured' => false,
                'translations' => [
                    'sn' => [
                        'question' => 'Ndomhan’ara kupi kana pombi yemvura yafa kana borehole risiri kushanda?',
                        'answer' => 'Mhan’arai ipapo ipapo kuKanzuru kuburikidza nerunhare (+263 77 123 4567), email, kana kumhan’ara kuna Kanzura wewadhi yenyu.',
                    ],
                ],
            ],

            // Governance FAQs
            [
                'question' => 'Can members of the public attend full Council meetings?',
                'answer' => 'Yes. Full Council meetings are open to the public and press in the public gallery under the Rural District Councils Act [Chapter 29:13], except for confidential committee proceedings held in committee.',
                'category' => 'governance',
                'department_id' => $adminDept?->id,
                'display_order' => 1,
                'is_published' => true,
                'is_featured' => false,
                'translations' => [
                    'sn' => [
                        'question' => 'Veruzhinji vanotenderwa here kupinda mumisangano yeFull Council?',
                        'answer' => 'Hongu. Misangano yeFull Council yakavhurika kuveruzhinji nevatori venhau.',
                    ],
                ],
            ],
        ];

        foreach ($faqs as $faqData) {
            $translations = $faqData['translations'] ?? [];
            unset($faqData['translations']);

            $faq = Faq::updateOrCreate(
                ['question' => $faqData['question']],
                $faqData
            );

            foreach ($translations as $locale => $fields) {
                foreach ($fields as $field => $val) {
                    ContentTranslation::updateOrCreate(
                        [
                            'translatable_type' => Faq::class,
                            'translatable_id' => $faq->id,
                            'locale' => $locale,
                            'field' => $field,
                        ],
                        [
                            'value' => $val,
                            'updated_by' => null,
                        ]
                    );
                }
            }
        }
    }
}
