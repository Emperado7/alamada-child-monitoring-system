<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('children', function (Blueprint $table) {
            $table->string('guardian_gender')->nullable()->after('guardian_contact');
            $table->string('guardian_relationship')->nullable()->after('guardian_gender');
        });

        DB::statement("ALTER TABLE children ADD CONSTRAINT chk_children_guardian_gender CHECK (guardian_gender IS NULL OR guardian_gender IN ('male','female'))");
        DB::statement("ALTER TABLE children ADD CONSTRAINT chk_children_guardian_relationship CHECK (
            guardian_relationship IS NULL OR guardian_relationship IN (
                'Father','Mother','Grandfather','Grandmother',
                'Uncle','Aunt','Sibling','Legal Guardian','Other'
            )
        )");
    }

    public function down(): void
    {
        Schema::table('children', function (Blueprint $table) {
            $table->dropColumn(['guardian_gender', 'guardian_relationship']);
        });
    }
};
