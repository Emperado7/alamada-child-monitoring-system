<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained('children')->cascadeOnDelete();
            $table->date('attendance_date');
            // NOTE: migration 000003 later removes 'late' — keep it here for historical
            // accuracy; the alter migration will tighten the constraint.
            $table->string('status')->default('present');
            $table->text('remarks')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // One attendance record per child per day
            $table->unique(['child_id', 'attendance_date']);
        });

        DB::statement("ALTER TABLE attendances ADD CONSTRAINT chk_attendances_status CHECK (status IN ('present','absent','late','excused'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
