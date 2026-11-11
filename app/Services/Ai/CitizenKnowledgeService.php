<?php

namespace App\Services\Ai;

use App\Models\BankAccount;
use App\Models\Department;
use App\Models\Faq;
use App\Models\PublicContact;
use App\Models\Service;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;

class CitizenKnowledgeService
{
    /**
     * Retrieve relevant council context grounded on the citizen query.
     */
    public function getCouncilContext(): string
    {
        return Cache::remember('citizen_council_knowledge_base', 300, function (): string {
            $sections = [];

            // 1. General Council Info
            $settings = SiteSetting::allKeyed();
            $phone = $settings['contact_phone'] ?? '+263 77 000 0000';
            $email = $settings['contact_email'] ?? 'info@mutokordc.gov.zw';
            $address = $settings['contact_address'] ?? 'Mutoko Growth Point, Zimbabwe';
            $hours = $settings['operating_hours'] ?? 'Monday - Friday: 08:00 - 16:30';

            $sections[] = "=== MUTOKO RDC OFFICIAL CONTACT & LOCATION ===\n"
                ."Head Office: {$address}\n"
                ."Official Phone: {$phone}\n"
                ."Official Email: {$email}\n"
                ."Office Hours: {$hours}";

            // 2. Verified Bank Accounts for Ratepayers
            $bankAccounts = BankAccount::query()->public()->orderBy('display_order')->get();
            if ($bankAccounts->isNotEmpty()) {
                $bankLines = ['=== APPROVED COUNCIL BANK ACCOUNTS (FOR RATES & BILLING) ==='];
                foreach ($bankAccounts as $acc) {
                    $bankLines[] = "- Bank: {$acc->bank_name} | Account Name: {$acc->account_name} | Account Number: {$acc->account_number} | Branch: {$acc->branch_name} | Currency: {$acc->currency}".($acc->purpose ? " ({$acc->purpose})" : '');
                }
                $sections[] = implode("\n", $bankLines);
            }

            // 3. Departments & Responsibilities
            $departments = Department::query()->orderBy('public_display_order')->get();
            if ($departments->isNotEmpty()) {
                $deptLines = ['=== COUNCIL DEPARTMENTS & SERVICES ==='];
                foreach ($departments as $dept) {
                    $name = $dept->public_name ?: $dept->name;
                    $summary = $dept->public_summary ?: $dept->description;
                    $deptLines[] = "- Department: {$name}\n  Summary: {$summary}";
                }
                $sections[] = implode("\n", $deptLines);
            }

            // 4. Council Public Services
            $services = Service::query()->public()->orderBy('display_order')->get();
            if ($services->isNotEmpty()) {
                $serviceLines = ['=== KEY COUNCIL SERVICES & REQUIREMENTS ==='];
                foreach ($services as $srv) {
                    $requirements = is_array($srv->requirements) ? implode(', ', array_map(fn ($r) => is_array($r) ? ($r['value'] ?? '') : $r, $srv->requirements)) : '';
                    $steps = is_array($srv->steps) ? implode(' -> ', array_map(fn ($s) => is_array($s) ? ($s['value'] ?? '') : $s, $srv->steps)) : '';
                    $srvText = "- Service: {$srv->name}\n  Summary: {$srv->summary}";
                    if ($requirements !== '') {
                        $srvText .= "\n  Requirements: {$requirements}";
                    }
                    if ($steps !== '') {
                        $srvText .= "\n  Procedure Steps: {$steps}";
                    }
                    if ($srv->fees_information) {
                        $srvText .= "\n  Fees: {$srv->fees_information}";
                    }
                    $serviceLines[] = $srvText;
                }
                $sections[] = implode("\n", $serviceLines);
            }

            // 5. Frequently Asked Questions
            $faqs = Faq::query()->public()->orderBy('display_order')->limit(25)->get();
            if ($faqs->isNotEmpty()) {
                $faqLines = ['=== FREQUENTLY ASKED QUESTIONS & ANSWERS ==='];
                foreach ($faqs as $faq) {
                    $faqLines[] = "Q: {$faq->question}\nA: {$faq->answer}";
                }
                $sections[] = implode("\n\n", $faqLines);
            }

            // 6. Emergency & Public Contacts
            $contacts = PublicContact::query()->public()->orderBy('display_order')->get();
            if ($contacts->isNotEmpty()) {
                $contactLines = ['=== SPECIFIC COUNCIL DESK CONTACTS ==='];
                foreach ($contacts as $contact) {
                    $contactLines[] = "- {$contact->office}: {$contact->type} - {$contact->value}";
                }
                $sections[] = implode("\n", $contactLines);
            }

            return implode("\n\n", $sections);
        });
    }
}
