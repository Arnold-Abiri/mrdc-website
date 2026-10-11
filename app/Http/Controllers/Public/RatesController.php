<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Document;
use App\Models\Faq;
use App\Models\Page;
use Inertia\Inertia;
use Inertia\Response;

class RatesController extends Controller
{
    public function index(): Response
    {
        $page = Page::query()->with('translations')->public()->where('slug', 'rates-information')->first();
        $schedules = Document::query()->with('translations')->public()->whereIn('category', ['bylaw', 'report', 'plan'])->orderByDesc('published_at')->limit(10)->get(['id', 'slug', 'title', 'category', 'reference_date'])->map(fn (Document $document): array => $document->toLocalizedArray(['slug', 'title', 'category', 'reference_date']));

        $bankAccounts = BankAccount::query()
            ->public()
            ->orderBy('display_order')
            ->orderBy('bank_name')
            ->get([
                'id',
                'bank_name',
                'account_name',
                'account_number',
                'branch_name',
                'branch_code',
                'currency',
                'category',
                'purpose',
            ]);

        $billingFaqs = Faq::query()
            ->with('translations')
            ->public()
            ->where('category', 'billing')
            ->orderBy('display_order')
            ->orderBy('id')
            ->get()
            ->map(fn (Faq $faq): array => [
                'id' => $faq->id,
                'question' => $faq->translated('question'),
                'answer' => $faq->translated('answer'),
            ]);

        return Inertia::render('Rates', [
            'page' => $page ? $page->toLocalizedArray(['slug', 'title', 'summary', 'blocks']) : null,
            'schedules' => $schedules,
            'bankAccounts' => $bankAccounts,
            'billingFaqs' => $billingFaqs,
        ]);
    }
}
