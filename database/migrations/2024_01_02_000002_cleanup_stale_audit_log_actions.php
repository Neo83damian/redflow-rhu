<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\AuditLog;

// One-time cleanup: earlier passes logged entries with action='View' and
// action='Edit', both since renamed away (the Audit Log now only uses
// Create/Update/Change/Export). Removes those old stale entries so they
// don't keep showing up as unrecognized/mislabeled rows.
return new class extends Migration
{
    public function up(): void
    {
        AuditLog::whereIn('action', ['View', 'Edit', 'change_password'])->delete();
    }

    public function down(): void
    {
        // Not reversible — these were stale entries meant to be removed.
    }
};
