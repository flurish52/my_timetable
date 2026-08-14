<?php

namespace App\Console\Commands;

use App\Models\ScanAttempt;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CleanupScanAttemptFiles extends Command
{
    /**
     * Retention window from the original design: images are kept up to 48h
     * on failure/rejection to allow a retry without re-upload, then purged
     * regardless of status. Successful scans already null file_paths and
     * delete their files immediately in ScanController::store(), so this
     * command is really only ever cleaning up 'failed'/'rejected' rows —
     * but it checks all statuses defensively in case that invariant ever
     * breaks elsewhere.
     */
    private const RETENTION_HOURS = 48;

    protected $signature = 'scan:cleanup-files {--dry-run : Show what would be deleted without deleting anything}';

    protected $description = 'Delete temp scan images older than 48h and null their file_paths';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $cutoff = now()->subHours(self::RETENTION_HOURS);
        $deletedFiles = 0;
        $touchedRows = 0;

        ScanAttempt::query()
            ->whereNotNull('file_paths')
            ->where('created_at', '<', $cutoff)
            ->chunkById(100, function ($attempts) use (&$deletedFiles, &$touchedRows, $dryRun) {
                foreach ($attempts as $attempt) {
                    $paths = $attempt->file_paths ?? [];

                    if (! is_array($paths)) {
                        $this->warn("Scan attempt {$attempt->id}: file_paths is not an array (got ".gettype($paths).") — check the model cast. Skipping.");
                        continue;
                    }

                    foreach ($paths as $path) {
                        $exists = Storage::disk('local')->exists($path);

                        if ($dryRun) {
                            $this->line(($exists ? '[would delete] ' : '[missing, would skip] ').$path);
                            if ($exists) $deletedFiles++;
                            continue;
                        }

                        try {
                            if ($exists) {
                                Storage::disk('local')->delete($path);
                                $deletedFiles++;
                            }
                        } catch (\Throwable $e) {
                            // Don't let one bad path abort the whole run —
                            // log it and keep going, still null the column
                            // below so we don't retry a broken path forever.
                            Log::warning('Scan cleanup: failed to delete file', [
                                'scan_attempt_id' => $attempt->id,
                                'path' => $path,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }

                    if (! $dryRun) {
                        $attempt->update(['file_paths' => null]);
                    }
                    $touchedRows++;
                }
            });

        $prefix = $dryRun ? '[DRY RUN] Would delete' : 'Deleted';
        $this->info("Scan cleanup: {$prefix} {$deletedFiles} files across {$touchedRows} scan attempts older than ".self::RETENTION_HOURS.'h.');

        if (! $dryRun) {
            Log::info('Scan cleanup completed', [
                'deleted_files' => $deletedFiles,
                'touched_rows' => $touchedRows,
                'cutoff' => $cutoff->toDateTimeString(),
            ]);
        }

        return self::SUCCESS;
    }
}
