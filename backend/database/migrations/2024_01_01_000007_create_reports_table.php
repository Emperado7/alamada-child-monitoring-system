<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_type');
            $table->string('title');
            $table->json('filters')->nullable();   // stores age/gender/year filter params
            $table->string('file_path')->nullable();
            $table->string('format')->default('pdf');
            $table->string('status')->default('generated');
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('generated_at')->useCurrent();
            $table->timestamps();
        });

        DB::statement("ALTER TABLE reports ADD CONSTRAINT chk_reports_report_type CHECK (report_type IN ('enrollment','attendance','activity','summary'))");
        DB::statement("ALTER TABLE reports ADD CONSTRAINT chk_reports_format       CHECK (format      IN ('pdf','excel','csv'))");
        DB::statement("ALTER TABLE reports ADD CONSTRAINT chk_reports_status       CHECK (status      IN ('generated','pending','failed'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
