<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained('children')->cascadeOnDelete();
            $table->date('activity_date');
            $table->enum('activity_type', [
                'Building Blocks',
                'Logico Piccolo',
                'Drawing and Coloring',
                'Writing Improvement',
                'Socialization',
                'Counting Numbers',
                'Identifying Alphabets',
                'Identifying Shapes',
                'Addition',
                'Subtraction',
                'Maze Tracing',
                'Puzzles',
                'Other'
            ]);
            $table->enum('completion_status', ['completed', 'in_progress', 'not_started'])->default('completed');
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
