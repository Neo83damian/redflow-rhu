<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use Illuminate\Console\Command;

// Keeps only the most recent 5,000 Audit Log rows — without this, a busy
// hospital using REDFLOW for months/years would build up an ever-growing
// table that eventually slows down every Audit Log query, which is exactly
// the kind of "lag at scale" this command exists to prevent. Schedule it
// in routes/console.php:
//   Schedule::command('audit-log:trim')->daily();
class TrimAuditLog extends Command
{
    protected $signature = 'audit-log:trim {--keep=5000}';
    protected $description = 'Deletes old Audit Log entries beyond the most recent N (default 5000)';

    public function handle(): void
    {
        $keep = (int) $this->option('keep');
        $cutoffId = AuditLog::orderByDesc('id')->skip($keep)->take(1)->value('id');

        if ($cutoffId) {
            $deleted = AuditLog::where('id', '<', $cutoffId)->delete();
            $this->info("Trimmed {$deleted} old Audit Log entr(ies), keeping the most recent {$keep}.");
        } else {
            $this->info('Nothing to trim — Audit Log is within the keep limit.');
        }
    }
}
