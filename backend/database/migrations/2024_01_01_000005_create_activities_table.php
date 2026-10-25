<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained('children')->cascadeOnDelete();
            $table->date('activity_date');
            $table->string('activity_type');
            $table->string('completion_status')->default('completed');
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        DB::statement("
            ALTER TABLE activities ADD CONSTRAINT chk_activities_activity_type CHECK (
                activity_type IN (
                    'Building Blocks','Logico Piccolo','Drawing and Coloring',
                    'Writing Improvement','Socialization','Counting Numbers',
                    'Identifying Alphabets','Identifying Shapes','Addition',
                    'Subtraction','Maze Tracing','Puzzles','Other'
                )
            )
        ");
        DB::statement("ALTER TABLE activities ADD CONSTRAINT chk_activities_completion_status CHECK (completion_status IN ('completed','in_progress','not_started'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
