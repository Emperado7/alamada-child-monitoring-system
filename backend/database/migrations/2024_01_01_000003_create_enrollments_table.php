<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained('children')->cascadeOnDelete();
            $table->date('enrollment_date');
            // PostgreSQL has no YEAR type — smallInteger stores a 4-digit year fine
            $table->smallInteger('school_year');
            $table->string('status')->default('pending');
            $table->text('remarks')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        DB::statement("ALTER TABLE enrollments ADD CONSTRAINT chk_enrollments_status CHECK (status IN ('pending','approved','rejected','graduated'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
