<?php

namespace App\Listeners;

use App\Events\AttachmentEvent;
use App\Services\AttachmentService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class AttachmentListener
{
    /**
     * Create the event listener.
     */
    public function __construct(protected AttachmentService $service)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AttachmentEvent $event): void
    {
        $filesArray = Arr::wrap($event->files);
        $fileCount = count($filesArray);

        if ($fileCount > 0) {
            Log::info("$fileCount file(s) are present in the AttachmentListener constructor.");
        } else {
            Log::info('No files are present in the AttachmentListener constructor.');
        }

        $this->service->uploadFile(
            $filesArray,
            $event->relation,
            $event->path,
            $event->identifier
        );
    }
}
