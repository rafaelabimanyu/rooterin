<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:phantom-sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trigger Encrypted Dump Engine and Phantom Cloud Sync for 100% Data Availability.';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\Backup\DatabaseBackupService $backupService)
    {
        $this->info('[!] Initiating RooterIN Encrypted Dump Engine...');
        
        $encryptedPayloadPath = $backupService->generateEncryptedSnapshot();
        
        if ($encryptedPayloadPath) {
            $this->info('[+] Encryption Successful. Deploying to Phantom Cloud Sync...');
            $syncSuccess = $backupService->syncToCloud($encryptedPayloadPath);
            
            if ($syncSuccess) {
                $this->info('[SUCCESS] Database payload verified and safely archived.');
                \Illuminate\Support\Facades\Cache::put('last_successful_backup', now());
            } else {
                $this->error('[CRITICAL] Phantom Cloud Sync Failed.');
            }
        } else {
            $this->error('[CRITICAL] Dump Engine Execution Failed.');
        }
    }
}
