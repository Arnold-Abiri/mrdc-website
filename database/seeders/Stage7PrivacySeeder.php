<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

/**
 * Stage 7 Privacy Policy page.
 *
 * Professional, fully articulated privacy statement outlining data collection,
 * public enquiry safeguards, citizen record protections, and digital security practices.
 */
class Stage7PrivacySeeder extends Seeder
{
    public function run(): void
    {
        $page = [
            'slug' => 'privacy-policy',
            'title' => 'Privacy Policy & Citizen Data Protection',
            'summary' => 'Comprehensive statement on how Mutoko Rural District Council collects, uses, protects, and manages personal and enquiry data.',
            'blocks' => [
                ['type' => 'heading', 'text' => 'Our Commitment to Citizen Privacy'],
                ['type' => 'paragraph', 'text' => 'Mutoko Rural District Council is committed to respecting and safeguarding the personal privacy of all residents, ratepayers, investors, and visitors who interact with our municipal web portals, electronic services, and administrative registry. This policy sets out the principles governing how personal data is collected, processed, and safeguarded in accordance with the laws of Zimbabwe, including the Cyber and Data Protection Act [Chapter 12:07].'],
                ['type' => 'heading', 'text' => 'Information We Collect and How It Is Used'],
                ['type' => 'paragraph', 'text' => 'We collect personal information solely to provide effective public service delivery, process municipal applications, and respond to citizen enquiries: 1. Public Enquiries & Service Requests: When you submit enquiries, rate queries, fault reports, or service feedback through our Contact and Feedback forms, we collect your name, email address, telephone contact, and ward/location details. This data is strictly used by designated council officers to investigate and respond to your request. 2. Procurement & Tenders: Submissions for council tenders, vendor registrations, or expressions of interest require statutory company details, tax clearances, and contact personnel information for compliance auditing. 3. Employment & Industrial Attachments: Job applications submitted to recruitment@mutokordc.co.zw are retained confidentially by Human Resources solely for merit-based evaluation and auditing.'],
                ['type' => 'heading', 'text' => 'Data Confidentiality & Protection Standards'],
                ['type' => 'paragraph', 'text' => 'Personal information submitted to Mutoko Rural District Council is treated with strict confidentiality. Council never sells, leases, or trades citizen data to third parties. Information is only disclosed to authorized law enforcement or national oversight agencies where explicitly mandated by court order or statutory requirements under Zimbabwean law.'],
                ['type' => 'heading', 'text' => 'Website Analytics & Cookie Policy'],
                ['type' => 'paragraph', 'text' => 'Our website uses essential first-party cookies to manage secure sessions, prevent automated spam, and preserve visitor language preferences (English, Shona, Ndebele). We record aggregate, anonymous performance statistics (such as total page visits, document downloads, and device types) to optimize website usability. No tracking cookies or commercial ad networks are installed.'],
                ['type' => 'heading', 'text' => 'Citizen Data Rights & Inquiries'],
                ['type' => 'paragraph', 'text' => 'Residents and stakeholders have the right to request clarification on any personal data held by council, verify the accuracy of their ratepayer records, or report privacy concerns. All privacy-related inquiries should be directed to the Council Secretary or Data Protection Officer at Stand 366 Mutoko Centre.'],
                ['type' => 'cta', 'text' => 'Contact the Council Secretariat', 'url' => '/contact'],
            ],
            'seo_title' => 'Privacy Policy & Citizen Data Protection | Mutoko RDC',
            'meta_description' => 'Official privacy policy and citizen data protection principles for Mutoko Rural District Council.',
            'status' => 'published',
            'verification_status' => 'publishable',
            'published_at' => now(),

        ];

        Page::query()->updateOrCreate(
            ['slug' => $page['slug']],
            $page
        );
    }
}
