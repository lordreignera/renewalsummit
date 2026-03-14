<?php

namespace App\Console\Commands;

use App\Models\Registration;
use App\Services\QrCodeService;
use Illuminate\Console\Command;

class RegenerateQrCodes extends Command
{
    protected $signature   = 'qr:regenerate {--all : Also regenerate for draft/pending registrations}';
    protected $description = 'Regenerate QR code PNGs for all paid/checked-in registrations';

    public function handle(QrCodeService $qrService): int
    {
        $query = Registration::whereIn('status', ['paid', 'checked_in']);

        if ($this->option('all')) {
            $query = Registration::query();
        }

        $registrations = $query->get();

        if ($registrations->isEmpty()) {
            $this->warn('No registrations found.');
            return 0;
        }

        $this->info("Regenerating QR codes for {$registrations->count()} registration(s)...");
        $bar = $this->output->createProgressBar($registrations->count());
        $bar->start();

        $success = 0;
        $failed  = 0;

        foreach ($registrations as $reg) {
            try {
                $path = $qrService->generateForRegistration($reg);
                $reg->update(['qr_code_path' => $path]);
                $success++;
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Failed for {$reg->reference}: {$e->getMessage()}");
                $failed++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✓ Done — {$success} regenerated, {$failed} failed.");

        return 0;
    }
}
