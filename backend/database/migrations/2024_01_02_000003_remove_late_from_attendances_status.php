<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Remove 'late' from the attendances.status allowed values.
     *
     * Laravel's ->change() uses DBAL under the hood and emits MySQL-specific
     * ENUM DDL, which fails on PostgreSQL.  We use raw SQL instead:
     *   1. Re-assign any existing 'late' rows to 'present'
     *   2. Drop the old CHECK constraint
     *   3. Add a new CHECK constraint without 'late'
     */
    public function up(): void
    {
        // Step 1 – clean up any 'late' rows before tightening the constraint
        DB::table('attendances')->where('status', 'late')->update(['status' => 'present']);

        // Step 2 – drop the constraint added by the create-table migration
        DB::statement('ALTER TABLE attendances DROP CONSTRAINT IF EXISTS chk_attendances_status');

        // Step 3 – re-add it without 'late'
        DB::statement("ALTER TABLE attendances ADD CONSTRAINT chk_attendances_status CHECK (status IN ('present','absent','excused'))");
    }

    public function down(): void
    {
        // Restore the original constraint that includes 'late'
        DB::statement('ALTER TABLE attendances DROP CONSTRAINT IF EXISTS chk_attendances_status');
        DB::statement("ALTER TABLE attendances ADD CONSTRAINT chk_attendances_status CHECK (status IN ('present','absent','late','excused'))");
    }
};
