<?php

namespace App\Domain\Cms;

use App\Domain\Identity\AuditWriter;
use App\Models\Faq;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class FaqManager
{
    public function create(User $actor, array $input): Faq
    {
        Gate::forUser($actor)->authorize('create', Faq::class);
        $data = $this->validate($input);

        return DB::transaction(function () use ($actor, $data): Faq {
            $faq = new Faq($data);
            $faq->save();
            app(AuditWriter::class)->record($actor, 'faqs.created', $faq, ['question' => $faq->question]);

            return $faq;
        });
    }

    public function update(User $actor, Faq $faq, array $input): Faq
    {
        Gate::forUser($actor)->authorize('update', $faq);
        $data = $this->validate($input);

        return DB::transaction(function () use ($actor, $faq, $data): Faq {
            $faq->fill($data);
            $faq->save();
            app(AuditWriter::class)->record($actor, 'faqs.updated', $faq, ['question' => $faq->question]);

            return $faq;
        });
    }

    public function delete(User $actor, Faq $faq): void
    {
        Gate::forUser($actor)->authorize('delete', $faq);

        DB::transaction(function () use ($actor, $faq): void {
            $faq->translations()->delete();
            $faq->delete();
            app(AuditWriter::class)->record($actor, 'faqs.deleted', $faq, ['question' => $faq->question]);
        });
    }

    private function validate(array $input): array
    {
        return Validator::make($input, [
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string', 'max:50000'],
            'category' => ['required', 'string', Rule::in(array_keys(Faq::CATEGORIES))],
            'department_id' => ['nullable', 'exists:departments,id'],
            'display_order' => ['required', 'integer', 'min:0', 'max:100000'],
            'is_published' => ['required', 'boolean'],
            'is_featured' => ['required', 'boolean'],
        ])->validate();
    }
}
