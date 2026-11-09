<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The Supabase database has a PL/pgSQL trigger (notify_parent_attendance)
 * that fires on attendance inserts and creates notifications with
 * recipient_group = 'individual'. This violates the existing CHECK constraint
 * which only allows ('all','parents','staff','specific').
 *
 * Fix: drop the old constraint and add a new one that also allows 'individual'.
 */
return new class extends Migration {
    public function up(): void
    {
        // Drop the old constraint
        DB::statement('ALTER TABLE notifications DROP CONSTRAINT IF EXISTS chk_notifications_recipient_group');

        // Add updated constraint that includes 'individual'
        DB::statement("ALTER TABLE notifications ADD CONSTRAINT chk_notifications_recipient_group CHECK (
            recipient_group IN ('all', 'parents', 'staff', 'specific', 'individual')
        )");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE notifications DROP CONSTRAINT IF EXISTS chk_notifications_recipient_group');

        // Restore original constraint
        DB::statement("ALTER TABLE notifications ADD CONSTRAINT chk_notifications_recipient_group CHECK (
            recipient_group IN ('all', 'parents', 'staff', 'specific')
        )");
    }
};
