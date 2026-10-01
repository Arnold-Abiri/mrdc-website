<?php

namespace App\Domain\Cms;

use App\Domain\Identity\AuditWriter;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\Page;
use App\Models\PageRevision;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PageManager
{
    public function create(User $actor, array $input): Page
    {
        Gate::forUser($actor)->authorize('create', Page::class);
        $data = $this->validate($input);
        abort_unless(app(DataScopeAuthorizer::class)->allows($actor, 'pages.create', $data['department_id'] ?? null, $actor->id), 403);

        return DB::transaction(function () use ($actor, $data): Page {
            $page = new Page($data);
            $page->created_by = $actor->id;
            $page->updated_by = $actor->id;
            $page->save();
            $this->revise($page, $actor);
            app(AuditWriter::class)->record($actor, 'pages.created', $page, ['slug' => $page->slug]);

            return $page;
        });
    }

    public function update(User $actor, Page $page, array $input): Page
    {
        Gate::forUser($actor)->authorize('update', $page);
        $data = $this->validate($input, $page);
        abort_unless(app(DataScopeAuthorizer::class)->allows($actor, 'pages.update', $data['department_id'] ?? null, $page->created_by), 403);

        return DB::transaction(function () use ($actor, $page, $data): Page {
            $page = Page::query()->lockForUpdate()->findOrFail($page->id);
            Gate::forUser($actor)->authorize('update', $page);
            $page->fill($data);
            $page->status = 'draft';
            $page->published_at = null;
            $page->verification_status = 'demo';
            $page->updated_by = $actor->id;
            $page->save();
            $this->revise($page, $actor);
            app(AuditWriter::class)->record($actor, 'pages.updated', $page, ['slug' => $page->slug]);

            return $page;
        });
    }

    public function setStatus(User $actor, Page $page, string $status): Page
    {
        Gate::forUser($actor)->authorize('publish', $page);
        abort_unless(in_array($status, ['published', 'unpublished', 'archived'], true), 422);

        return DB::transaction(function () use ($actor, $page, $status): Page {
            $page = Page::query()->lockForUpdate()->findOrFail($page->id);
            Gate::forUser($actor)->authorize('publish', $page);
            abort_if($status === 'published' && $page->verification_status !== 'publishable' && app()->environment('production'), 422, 'Only publishable content may be published in production.');
            $page->status = $status;
            $page->published_at = $status === 'published' ? now() : null;
            $page->updated_by = $actor->id;
            $page->save();
            $this->revise($page, $actor);
            app(AuditWriter::class)->record($actor, 'pages.'.$status, $page, ['slug' => $page->slug]);

            return $page;
        });
    }

    public function setVerification(User $actor, Page $page, string $verificationStatus): Page
    {
        Gate::forUser($actor)->authorize('verify', $page);
        abort_unless(in_array($verificationStatus, ['demo', 'verified', 'publishable'], true), 422);

        return DB::transaction(function () use ($actor, $page, $verificationStatus): Page {
            $page = Page::query()->lockForUpdate()->findOrFail($page->id);
            Gate::forUser($actor)->authorize('verify', $page);
            $page->verification_status = $verificationStatus;
            if ($verificationStatus !== 'publishable' && app()->environment('production')) {
                $page->status = 'unpublished';
                $page->published_at = null;
            }
            $page->updated_by = $actor->id;
            $page->save();
            $this->revise($page, $actor);
            app(AuditWriter::class)->record($actor, 'pages.verification_changed', $page, ['state' => $verificationStatus]);

            return $page;
        });
    }

    public function restore(User $actor, Page $page, PageRevision $revision): Page
    {
        Gate::forUser($actor)->authorize('update', $page);
        abort_unless($revision->page_id === $page->id, 404);
        $snapshot = $revision->getAttribute('snapshot');
        abort_unless(is_array($snapshot), 422);
        $data = $this->validate($snapshot, $page);

        return DB::transaction(function () use ($actor, $page, $data): Page {
            $page = Page::query()->lockForUpdate()->findOrFail($page->id);
            Gate::forUser($actor)->authorize('update', $page);
            $page->fill($data);
            $page->status = 'draft';
            $page->published_at = null;
            $page->verification_status = 'demo';
            $page->updated_by = $actor->id;
            $page->save();
            $this->revise($page, $actor);
            app(AuditWriter::class)->record($actor, 'pages.restored', $page, ['slug' => $page->slug]);

            return $page;
        });
    }

    private function validate(array $input, ?Page $page = null): array
    {
        $data = Validator::make($input, [
            'slug' => ['required', 'string', 'max:160', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('pages', 'slug')->ignore($page?->id)],
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:1000'],
            'blocks' => ['required', 'array', 'max:50'],
            'blocks.*.type' => ['required', Rule::in(['heading', 'paragraph', 'cta'])],
            'blocks.*.text' => ['required', 'string', 'max:10000'],
            'blocks.*.url' => ['nullable', 'string', 'max:2048'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'department_id' => ['nullable', 'integer', Rule::exists('departments', 'id')->where('status', 'active')],
        ])->validate();
        foreach ($data['blocks'] as $block) {
            if ($block['type'] === 'cta' && (! isset($block['url']) || ! str_starts_with($block['url'], '/') || str_starts_with($block['url'], '//'))) {
                throw ValidationException::withMessages(['blocks' => 'Links require an internal URL.']);
            }
        }

        return $data;
    }

    private function revise(Page $page, User $actor): void
    {
        $page->revisions()->create([
            'number' => ((int) $page->revisions()->max('number')) + 1,
            'snapshot' => $page->only(['slug', 'title', 'summary', 'blocks', 'status', 'verification_status', 'published_at', 'seo_title', 'meta_description', 'department_id']),
            'editor_id' => $actor->id,
        ]);
    }
}
