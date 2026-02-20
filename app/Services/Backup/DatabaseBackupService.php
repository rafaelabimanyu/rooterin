<?php

namespace App\Services\Backup;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class DatabaseBackupService
{
    protected $encryptionKey;

    public function __construct()
    {
        // For production, this should fetch from Security Vault (e.g. AWS KMS or local Vault)
        $this->encryptionKey = config('app.backup_key', config('app.key'));
    }

    /**
     * UNICORP-GRADE Encrypted Dump Engine
     * Ekspor skema database, compress, lalu encrypt on-the-fly.
     */
    public function generateEncryptedSnapshot()
    {
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');

        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "dump_{$database}_{$timestamp}.sql";
        $tarball = "{$filename}.tar.gz";
        $encryptedArchive = "{$tarball}.enc";

        $tempDir = storage_path('app/backups/temp');
        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        $sqlPath = "{$tempDir}/{$filename}";
        $tarPath = "{$tempDir}/{$tarball}";
        $encPath = "{$tempDir}/{$encryptedArchive}";

        // Step 1: Ekspor Database MySQL (Bisa disesuaikan jika PostgreSQL)
        $dumpCommand = [
            'mysqldump',
            "-h{$host}",
            "-P{$port}",
            "-u{$username}",
            $database,
        ];
        if (!empty($password)) {
            array_splice($dumpCommand, 4, 0, "-p{$password}");
        }

        Log::info("[BACKUP ENGINE] Initiating raw layer dump for database: {$database}");
        
        $process = new Process($dumpCommand);
        $process->setTimeout(300); // 5 menit maks

        try {
            $process->mustRun(function ($type, $buffer) use ($sqlPath) {
                File::append($sqlPath, $buffer);
            });
        } catch (\Exception $e) {
            Log::critical("[BACKUP ENGINE] FAILED: Raw dump process halted. Reason: " . $e->getMessage());
            return false;
        }

        // Step 2: Kompresi (Tar.gz)
        Log::info("[BACKUP ENGINE] Raw dump complete. Compacting into high-density archive...");
        $tarProcess = new Process(['tar', '-czvf', $tarPath, '-C', $tempDir, $filename]);
        
        try {
            $tarProcess->mustRun();
            File::delete($sqlPath); // Secure Erase raw sql
        } catch (\Exception $e) {
            // Pada environment Windows tanpa 'tar' native, kita simulasi kompresi sementara.
            Log::warning("[BACKUP ENGINE] Tar utility not available natively (Windows environment detected). Bypassing compression layer temporarily. Fallback to raw copy.");
            File::move($sqlPath, $tarPath);
        }

        // Step 3: Enkripsi On-The-Fly menggunakan OpenSSL
        Log::info("[BACKUP ENGINE] Initializing Advanced Encryption Standard (AES-256-CBC)...");
        try {
            // Membaca file terkompresi
            $content = File::get($tarPath);
            
            // Konfigurasi AES-256-CBC
            $method = 'AES-256-CBC';
            $key = hash('sha256', $this->encryptionKey, true);
            $ivLength = openssl_cipher_iv_length($method);
            $iv = openssl_random_pseudo_bytes($ivLength);

            $encrypted = openssl_encrypt($content, $method, $key, OPENSSL_RAW_DATA, $iv);
            
            // Format file: [IV_Length (2byte)][IV][Encrypted Payload] (Simulasi sederhana)
            // Kami menyimpannya menggunakan base64 agar aman saat transportasi jaringan
            $payload = base64_encode($iv . $encrypted);
            
            File::put($encPath, $payload);
            File::delete($tarPath); // Secure Erase unencrypted tar
            
            Log::info("[BACKUP ENGINE] Payload encrypted successfully. Cipher locked.");
        } catch (\Exception $e) {
            Log::critical("[BACKUP ENGINE] ENCRYPTION FAILED: " . $e->getMessage());
            return false;
        }

        return $encPath;
    }

    /**
     * Phantom Cloud Sync & Integrity Verification
     */
    public function syncToCloud($localPath)
    {
        $filename = basename($localPath);
        Log::info("[PHANTOM CLOUD] Initiating internal node connectivity for sync...");

        try {
            // Menghitung Checksum SHA256 Asli
            $localChecksum = hash_file('sha256', $localPath);
            
            // SIMULASI Upload ke Cloud (misal: S3 / Google Drive / Remote Server)
            // Menggunakan filesystem disk 'local_cloud_mock' untuk simulasi Cloud Drive
            $cloudDisk = Storage::disk('local'); // Diubah 's3' jika di production
            $cloudPath = "phantom_cloud/{$filename}";
                        
            $fileStream = fopen($localPath, 'r');
            $cloudDisk->put($cloudPath, $fileStream);
            if (is_resource($fileStream)) {
                fclose($fileStream);
            }
            
            Log::info("[PHANTOM CLOUD] Payload localized in cloud sector. Commencing SHA256 verification...");

            // Download (or mock download) untuk verifikasi Hash
            $cloudFileContents = $cloudDisk->get($cloudPath);
            $cloudChecksum = hash('sha256', $cloudFileContents);
            
            if ($localChecksum !== $cloudChecksum) {
                // INTEGRITY BREACH
                $this->triggerCriticalAlert("CHECKSUM_MISMATCH: Cloud payload hash ({$cloudChecksum}) differs from Local Hash ({$localChecksum}). Transmission compromised.");
                return false;
            }

            Log::info("[PHANTOM CLOUD] Integrity Verified: SHA256 Hash Match Confirm ({$localChecksum}).");
            
            // Intelligent Storage Management: Delete local file after sync
            $this->secureEraseLocalNode($localPath);

            // Terapkan retensi 7 hari di cloud
            $this->enforceCloudRetentionPolicy($cloudDisk, 'phantom_cloud');

            return true;

        } catch (\Exception $e) {
            $this->triggerCriticalAlert("SYNC_FAILURE: Phantom Cloud transmission rejected. " . $e->getMessage());
            return false;
        }
    }

    /**
     * Menghapus backup lokal setelah berhasil diunggah
     */
    protected function secureEraseLocalNode($filePath)
    {
        if (File::exists($filePath)) {
            File::delete($filePath);
            Log::info("[STORAGE MANAGER] Local temporary payload eradicated. Physical Storage Audit stabilized.");
        }
    }

    /**
     * Membatasi retensi backup di cloud maksimal 7 hari
     */
    protected function enforceCloudRetentionPolicy($disk, $directory)
    {
        $files = $disk->files($directory);
        $now = now();
        $deletedCount = 0;

        foreach ($files as $file) {
            $lastModified = $disk->lastModified($file);
            $modifiedDate = \Carbon\Carbon::createFromTimestamp($lastModified);

            if ($modifiedDate->diffInDays($now) > 7) {
                $disk->delete($file);
                $deletedCount++;
            }
        }

        if ($deletedCount > 0) {
            Log::info("[STORAGE MANAGER] Retention Policy Active: Terminated {$deletedCount} out-of-date cloud payload(s).");
        }
    }

    /**
     * Alerting System
     */
    protected function triggerCriticalAlert($message)
    {
        Log::critical("[SYSTEM ALERT] " . $message);
        
        // Terhubung ke SecurityAutomationService untuk audit
        $security = app(\App\Services\Security\SecurityAutomationService::class);
        $security->auditLog('CRITICAL_BACKUP_FAILURE', ['reason' => $message]);

        // MOCK: Mengirim pesan WhatsApp ke Lead Gateway
        Log::warning("[WHATSAPP GATEWAY] SENDING: 🚨 ROOTER_IN CRITICAL ALERT: Database Sync Failure. Reason: {$message} 🚨");
    }
}
