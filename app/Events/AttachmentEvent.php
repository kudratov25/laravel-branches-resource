<?php

namespace App\Events;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class AttachmentEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(public array|UploadedFile|null $files = null, public MorphOne|MorphMany|MorphToMany|null $relation = null, public string $path = 'files', public ?string $identifier = null)
    {
        $filesArray = Arr::wrap($files);
        $fileCount = count($filesArray);

        if ($fileCount > 0) {
            Log::info("$fileCount file(s) are present in the AttachmentEvent constructor.");
        } else {
            Log::info('No files are present in the AttachmentEvent constructor.');
        }
    }
}
