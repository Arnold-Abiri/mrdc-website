<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FaqController extends Controller
{
    public function index(Request $request): Response
    {
        $selectedCategory = $request->query('category');
        if (! is_string($selectedCategory) || ! array_key_exists($selectedCategory, Faq::CATEGORIES)) {
            $selectedCategory = null;
        }

        $faqs = Faq::query()
            ->with(['translations', 'department'])
            ->public()
            ->orderBy('display_order')
            ->orderBy('id')
            ->get()
            ->map(fn (Faq $faq): array => [
                'id' => $faq->id,
                'question' => $faq->translated('question'),
                'answer' => $faq->translated('answer'),
                'category' => $faq->category,
                'category_label' => Faq::CATEGORIES[$faq->category] ?? $faq->category,
                'department' => $faq->department?->name,
                'is_featured' => $faq->is_featured,
            ]);

        return Inertia::render('Faqs/Index', [
            'faqs' => $faqs,
            'categories' => Faq::CATEGORIES,
            'initialCategory' => $selectedCategory,
        ]);
    }
}
