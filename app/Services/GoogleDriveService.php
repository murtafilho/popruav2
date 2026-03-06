<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Support\Facades\Log;

class GoogleDriveService
{
    private const FOLDER_ID = '150so7yLUR23dwesO3xc6lCaClMjeVN5U';

    private const MIME_TYPES = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'bmp' => 'image/bmp',
    ];

    private ?Drive $driveService = null;

    public function getDriveService(): Drive
    {
        if ($this->driveService) {
            return $this->driveService;
        }

        $httpClient = new \GuzzleHttp\Client(['verify' => false]);

        $client = new Client;
        $client->setHttpClient($httpClient);
        $client->setApplicationName('POPRUA v2');
        $client->setScopes([Drive::DRIVE]);
        $client->setClientId(config('services.google_drive.client_id'));
        $client->setClientSecret(config('services.google_drive.client_secret'));
        $client->setAccessType('offline');

        $tokenPath = storage_path('app/google_drive_token.json');

        if (! file_exists($tokenPath)) {
            throw new \RuntimeException("Google Drive token not found at: {$tokenPath}");
        }

        $tokenData = json_decode(file_get_contents($tokenPath), true);

        // Normalizar formato do token
        if (isset($tokenData['token']) && ! isset($tokenData['access_token'])) {
            $tokenData = [
                'access_token' => $tokenData['token'],
                'refresh_token' => $tokenData['refresh_token'],
                'token_type' => 'Bearer',
                'scope' => implode(' ', $tokenData['scopes'] ?? []),
                'created' => time(),
            ];
        }

        $client->setAccessToken($tokenData);

        // Renovar token se expirado
        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            $newToken = $client->getAccessToken();
            $newToken['refresh_token'] = $client->getRefreshToken();
            file_put_contents($tokenPath, json_encode($newToken, JSON_PRETTY_PRINT));
            Log::info('GoogleDriveService: Token renovado automaticamente.');
        }

        $this->driveService = new Drive($client);

        return $this->driveService;
    }

    /**
     * Upload de arquivo para o Google Drive.
     *
     * @return array{id: string, name: string, webViewLink: string}
     */
    public function upload(string $localPath, string $driveFileName, ?string $folderId = null): array
    {
        $folderId = $folderId ?: self::FOLDER_ID;

        if (! file_exists($localPath)) {
            throw new \RuntimeException("File not found: {$localPath}");
        }

        $ext = strtolower(pathinfo($localPath, PATHINFO_EXTENSION));
        $mimeType = self::MIME_TYPES[$ext] ?? 'application/octet-stream';

        $metadata = new DriveFile([
            'name' => $driveFileName,
            'parents' => [$folderId],
        ]);

        $drive = $this->getDriveService();

        $file = $drive->files->create($metadata, [
            'data' => file_get_contents($localPath),
            'mimeType' => $mimeType,
            'uploadType' => 'multipart',
            'fields' => 'id, name, webViewLink, webContentLink',
        ]);

        return [
            'id' => $file->getId(),
            'name' => $file->getName(),
            'webViewLink' => $file->getWebViewLink(),
        ];
    }

    /**
     * Cria subpasta dentro da pasta principal do projeto.
     */
    public function createSubfolder(string $folderName, ?string $parentId = null): string
    {
        $parentId = $parentId ?: self::FOLDER_ID;

        $drive = $this->getDriveService();

        // Verificar se a subpasta ja existe
        $query = "name = '{$folderName}' and '{$parentId}' in parents and mimeType = 'application/vnd.google-apps.folder' and trashed = false";
        $existing = $drive->files->listFiles([
            'q' => $query,
            'fields' => 'files(id)',
        ]);

        if (count($existing->getFiles()) > 0) {
            return $existing->getFiles()[0]->getId();
        }

        $metadata = new DriveFile([
            'name' => $folderName,
            'mimeType' => 'application/vnd.google-apps.folder',
            'parents' => [$parentId],
        ]);

        $folder = $drive->files->create($metadata, [
            'fields' => 'id',
        ]);

        return $folder->getId();
    }
}
