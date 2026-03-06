<?php

namespace App\Jobs;

use App\Services\GoogleDriveService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class UploadMediaToDriveJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [30, 60, 120];

    public function __construct(
        public int $mediaId
    ) {}

    public function handle(GoogleDriveService $driveService): void
    {
        $media = Media::find($this->mediaId);

        if (! $media) {
            Log::warning("UploadMediaToDriveJob: Media {$this->mediaId} not found, skipping.");

            return;
        }

        if ($media->cloud_status === 'uploaded') {
            return;
        }

        $localPath = $media->getPath();

        if (! file_exists($localPath)) {
            Log::error("UploadMediaToDriveJob: Local file not found for media {$this->mediaId}: {$localPath}");
            $media->update(['cloud_status' => 'error']);

            return;
        }

        // Organizar em subpastas: vistorias/{vistoria_id}/
        $subfolderName = "vistoria_{$media->model_id}";
        $driveFileName = $media->file_name;

        try {
            $folderId = $driveService->createSubfolder($subfolderName);
            $result = $driveService->upload($localPath, $driveFileName, $folderId);

            $media->update([
                'cloud_disk' => 'google_drive',
                'cloud_path' => $result['id'],
                'cloud_status' => 'uploaded',
                'cloud_synced_at' => now(),
            ]);

            Log::info("UploadMediaToDriveJob: Media {$this->mediaId} uploaded to Drive: {$result['webViewLink']}");
        } catch (\Throwable $e) {
            Log::error("UploadMediaToDriveJob: Failed to upload media {$this->mediaId}: {$e->getMessage()}");
            $media->update(['cloud_status' => 'error']);

            throw $e;
        }
    }
}
