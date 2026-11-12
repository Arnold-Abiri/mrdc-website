<?php

namespace App\Services\Ai;

use App\Models\BankAccount;
use App\Models\Department;
use App\Models\Faq;
use App\Models\PublicContact;
use App\Models\Service;
use App\Models\SiteSetting;

class CitizenKnowledgeService
{
    /**
     * Retrieve relevant council context grounded on the citizen query.
     */
    public function getCouncilContext(): string
    {
        $sections = [];

        // 1. General Council Info
        $settings = SiteSetting::allKeyed();
        $contactLines = [];
        foreach (['physical_address' => 'Head Office', 'phone' => 'Official Phone', 'email' => 'Official Email', 'office_hours' => 'Office Hours'] as $key => $label) {
            $value = trim((string) ($settings[$key] ?? ''));
            if ($value !== '') {
                $contactLines[] = "{$label}: {$value}";
            }
        }
        if ($contactLines !== []) {
            $sections[] = "=== MUTOKO RDC OFFICIAL CONTACT & LOCATION ===\n".implode("\n", $contactLines);
        }

        // 2. Verified Bank Accounts for Ratepayers
        $bankAccounts = BankAccount::query()->public()->orderBy('display_order')->get();
        if ($bankAccounts->isNotEmpty()) {
            $bankLines = ['=== APPROVED COUNCIL BANK ACCOUNTS (FOR RATES & BILLING) ==='];
            foreach ($bankAccounts as $acc) {
                if ($acc->payment_type === 'mobile') {
                    $bankLines[] = "- Provider: {$acc->bank_name} | {$acc->code_label}: {$acc->account_number}\n{$acc->instructions}";

                    continue;
                }
                $bankLines[] = "- Bank: {$acc->bank_name} | Account Name: {$acc->account_name} | Account Number: {$acc->account_number} | Branch: {$acc->branch_name} | Currency: {$acc->currency}".($acc->purpose ? " ({$acc->purpose})" : '');
            }
            $sections[] = implode("\n", $bankLines);
        }

        // 3. Departments & Responsibilities
        $departments = Department::query()->where('status', 'active')->where('public_status', 'published')
            ->where('public_verification_status', 'publishable')->whereNotNull('public_published_at')
            ->where('public_published_at', '<=', now())->orderBy('public_display_order')->get();
        if ($departments->isNotEmpty()) {
            $deptLines = ['=== COUNCIL DEPARTMENTS & SERVICES ==='];
            foreach ($departments as $dept) {
                $name = $dept->public_name;
                $summary = $dept->public_summary;
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
    }
}
