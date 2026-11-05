<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Align the guardian_relationship CHECK constraint with the controller's Rule::in values.
 *
 * The original migration (2024_01_02_000002) used:
 *   'Father','Mother','Grandfather','Grandmother','Uncle','Aunt','Sibling','Legal Guardian','Other'
 *
 * The Admin\ChildController and Staff\EnrollmentController both validate with:
 *   'Mother','Father','Grandmother','Grandfather','Aunt','Uncle','Older Sibling','Guardian','Other'
 *
 * Mismatches: 'Sibling' → 'Older Sibling', 'Legal Guardian' → 'Guardian'
 * Submitting 'Older Sibling' or 'Guardian' would pass Laravel validation but fail the DB CHECK,
 * causing a 500 error.  This migration corrects the DB to match the application code.
 */
return new class extends Migration {
    public function up(): void
    {
        // Step 1 – migrate any existing rows with old values to their new equivalents
        DB::table('children')->where('guardian_relationship', 'Sibling')
            ->update(['guardian_relationship' => 'Older Sibling']);
        DB::table('children')->where('guardian_relationship', 'Legal Guardian')
            ->update(['guardian_relationship' => 'Guardian']);

        // Step 2 – drop the old constraint
        DB::statement('ALTER TABLE children DROP CONSTRAINT IF EXISTS chk_children_guardian_relationship');

        // Step 3 – add the corrected constraint matching controller Rule::in values
        DB::statement("ALTER TABLE children ADD CONSTRAINT chk_children_guardian_relationship CHECK (
            guardian_relationship IS NULL OR guardian_relationship IN (
                'Father','Mother','Grandmother','Grandfather','Aunt','Uncle',
                'Older Sibling','Guardian','Other'
            )
        )");
    }

    public function down(): void
    {
        // Restore original values
        DB::table('children')->where('guardian_relationship', 'Older Sibling')
            ->update(['guardian_relationship' => 'Sibling']);
        DB::table('children')->where('guardian_relationship', 'Guardian')
            ->update(['guardian_relationship' => 'Legal Guardian']);

        DB::statement('ALTER TABLE children DROP CONSTRAINT IF EXISTS chk_children_guardian_relationship');
        DB::statement("ALTER TABLE children ADD CONSTRAINT chk_children_guardian_relationship CHECK (
            guardian_relationship IS NULL OR guardian_relationship IN (
                'Father','Mother','Grandfather','Grandmother',
                'Uncle','Aunt','Sibling','Legal Guardian','Other'
            )
        )");
    }
};
