<?php

namespace App\Services;

use App\Models\Laporan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleDriveService
{
    /**
     * Mengunggah file laporan ke Google Drive API.
     * Jika kredensial belum dikonfigurasi di .env, sistem menyimpan status lokal dengan aman
     * dan memberikan pesan penjelasan yang informatif.
     *
     * @param string $localFilePath Path file di storage lokal
     * @param string $fileName Nama file yang akan ditampilkan di Google Drive
     * @param array $metadata Informasi tambahan (tahun_ajaran, semester, jenis_laporan)
     * @return array ['success' => bool, 'drive_file_id' => ?string, 'drive_url' => ?string, 'message' => string]
     */
    public static function uploadReport(string $localFilePath, string $fileName, array $metadata = []): array
    {
        $clientId = env('GOOGLE_DRIVE_CLIENT_ID');
        $clientSecret = env('GOOGLE_DRIVE_CLIENT_SECRET');
        $refreshToken = env('GOOGLE_DRIVE_REFRESH_TOKEN');
        $folderId = env('GOOGLE_DRIVE_FOLDER_ID');

        // Jika kredensial Google Drive API belum diisi di .env
        if (! $clientId || ! $clientSecret || ! $refreshToken) {
            return [
                'success' => false,
                'drive_file_id' => null,
                'drive_url' => null,
                'message' => 'Integrasi Google Drive belum aktif. Konfigurasikan GOOGLE_DRIVE_CLIENT_ID, SECRET, dan REFRESH_TOKEN di file .env untuk sinkronisasi otomatis.',
            ];
        }

        try {
            // 1. Dapatkan Access Token baru dari Google OAuth2 menggunakan Refresh Token
            $tokenResponse = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'refresh_token' => $refreshToken,
                'grant_type' => 'refresh_token',
            ]);

            if (! $tokenResponse->successful()) {
                Log::error('Google Drive OAuth failed: ' . $tokenResponse->body());
                return [
                    'success' => false,
                    'drive_file_id' => null,
                    'drive_url' => null,
                    'message' => 'Gagal mendapatkan akses token dari Google Drive: ' . $tokenResponse->json('error_description', 'Token tidak valid'),
                ];
            }

            $accessToken = $tokenResponse->json('access_token');

            // 2. Siapkan metadata file untuk Google Drive
            $fileMetadata = [
                'name' => $fileName,
                'parents' => $folderId ? [$folderId] : [],
                'description' => sprintf(
                    'Laporan Absensi UPTD SDN 2 Bati-Bati | TA: %s | Semester: %s | Jenis: %s',
                    $metadata['tahun_ajaran'] ?? '-',
                    $metadata['semester'] ?? '-',
                    $metadata['jenis_laporan'] ?? '-'
                ),
            ];

            // 3. Upload file menggunakan Google Drive Multipart Upload API
            $fileContent = file_get_contents($localFilePath);
            $mimeType = mime_content_type($localFilePath) ?: 'application/pdf';

            $uploadResponse = Http::withToken($accessToken)
                ->attach('metadata', json_encode($fileMetadata), 'metadata.json', ['Content-Type' => 'application/json; charset=UTF-8'])
                ->attach('file', $fileContent, $fileName, ['Content-Type' => $mimeType])
                ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&fields=id,webViewLink,name');

            if ($uploadResponse->successful()) {
                $driveData = $uploadResponse->json();
                return [
                    'success' => true,
                    'drive_file_id' => $driveData['id'] ?? null,
                    'drive_url' => $driveData['webViewLink'] ?? "https://drive.google.com/file/d/{$driveData['id']}/view",
                    'message' => 'Laporan berhasil diarsipkan ke Google Drive.',
                ];
            }

            Log::error('Google Drive Upload error: ' . $uploadResponse->body());
            return [
                'success' => false,
                'drive_file_id' => null,
                'drive_url' => null,
                'message' => 'Google Drive mengembalikan kesalahan: ' . ($uploadResponse->json('error.message') ?? 'Gagal mengunggah'),
            ];

        } catch (\Throwable $e) {
            Log::error('Google Drive Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'drive_file_id' => null,
                'drive_url' => null,
                'message' => 'Terjadi kesalahan saat menghubungkan ke Google Drive: ' . $e->getMessage(),
            ];
        }
    }
}
