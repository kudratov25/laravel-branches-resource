<?php

namespace App\Services;

use App\Models\Attachment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Arr;

final class AttachmentService
{
    public function uploadFile(array $files, MorphOne|MorphMany|MorphToMany $relation = null, string $path = 'files', string $identifier = null): array
    {
        $result = [];
        foreach ($files as $file) {
            $type     = $file->getClientOriginalExtension();
            $fileName = md5(time() . $file->getFilename()) . '.' . $type;
            $file->storeAs("uploads/{$path}", $fileName, ['disk' => 'public']);

            $data = [
                'title'            => $file->getClientOriginalName(),
                'size'             => $file->getSize(),
                'path'             => "uploads/{$path}/{$fileName}",
                'type'             => $file->extension(),
                'extra_identifier' => $identifier,
            ];

            $relation->create($data);
        }

        return $result;
    }


    public function destroy(array|int|Attachment|Collection $files): void
    {
        if (!$files instanceof Collection) {
            $files = Arr::wrap($files);
        }
        foreach ($files as $file) {
            $this->delete($file);
        }
    }

    public function delete(Attachment|int $attachment): void
    {
        if (!$attachment instanceof Attachment) {
            $attachment = Attachment::findOrFail($attachment);
        }
        $this->removeFile($attachment);

        Attachment::withoutEvents(function () use ($attachment) {
            $attachment->delete();
        });
    }

    private function removeFile(Attachment $model): void
    {
        @unlink(storage_path('app/public/' . $model->path));
    }
}
