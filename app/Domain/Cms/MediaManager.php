<?php

namespace App\Domain\Cms;

use App\Domain\Identity\AuditWriter;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MediaManager
{
    private const TYPES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'application/pdf' => 'pdf',
    ];

    public function upload(User $actor, UploadedFile $file, array $input): Media
    {
        Gate::forUser($actor)->authorize('create', Media::class);
        $data = Validator::make([...$input, 'file' => $file], [
            'file' => ['required', 'file', 'max:'.config('cms.max_upload_kb')],
            'title' => ['required', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:2000'],
            'department_id' => ['nullable', 'integer', Rule::exists('departments', 'id')->where('status', 'active')],
        ])->validate();
        abort_unless(app(DataScopeAuthorizer::class)->allows($actor, 'media.create', $data['department_id'] ?? null, $actor->id), 403);
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file->getRealPath());
        if (! is_string($mime) || ! isset(self::TYPES[$mime])) {
            throw ValidationException::withMessages(['file' => 'Only JPEG, PNG, WebP and PDF files are accepted.']);
        }
        $dimensions = str_starts_with($mime, 'image/') ? @getimagesize($file->getRealPath()) : false;
        if (str_starts_with($mime, 'image/') && $dimensions === false) {
            throw ValidationException::withMessages(['file' => 'The image could not be decoded.']);
        }
        $original = basename(str_replace('\\', '/', $file->getClientOriginalName()));
        $original = preg_replace('/[^A-Za-z0-9._-]/', '_', $original) ?: 'upload';
        $path = $file->storeAs('cms/'.date('Y/m'), Str::uuid().'.'.self::TYPES[$mime], config('cms.media_disk'));
        if ($path === false) {
            throw ValidationException::withMessages(['file' => 'The file could not be stored.']);
        }

        try {
            $media = new Media($data);
            $media->original_filename = mb_substr($original, 0, 255);
            $media->storage_path = $path;
            $media->mime_type = $mime;
            $media->size = $file->getSize();
            $media->width = $dimensions === false ? null : $dimensions[0];
            $media->height = $dimensions === false ? null : $dimensions[1];
            $media->department_id = $data['department_id'] ?? null;
            $media->uploaded_by = $actor->id;
            $media->save();
            app(AuditWriter::class)->record($actor, 'media.uploaded', $media, ['mime_type' => $mime, 'size' => $media->size]);

            return $media;
        } catch (\Throwable $exception) {
            Storage::disk(config('cms.media_disk'))->delete($path);
            throw $exception;
        }
    }

    public function update(User $actor, Media $media, array $input): Media
    {
        Gate::forUser($actor)->authorize('update', $media);
        $data = Validator::make($input, [
            'title' => ['required', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:2000'],
        ])->validate();
        $media->fill($data)->save();
        app(AuditWriter::class)->record($actor, 'media.updated', $media);

        return $media;
    }

    public function archive(User $actor, Media $media): Media
    {
        Gate::forUser($actor)->authorize('update', $media);
        $media->status = 'archived';
        $media->save();
        app(AuditWriter::class)->record($actor, 'media.archived', $media);

        return $media;
    }
}
