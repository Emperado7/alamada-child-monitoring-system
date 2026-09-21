<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('children', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female']);
            $table->string('address');
            $table->string('guardian_name');
            $table->string('guardian_contact');
            $table->enum('diagnosis', [
                'ADHD',
                'Autism',
                'Mental Disability',
                'Cerebral Palsy',
                'Blindness',
                'Other'
            ]);
            $table->text('diagnosis_notes')->nullable();
            $table->string('photo')->nullable();
            $table->enum('status', ['active', 'inactive', 'graduated'])->default('active');
            // Staff assigned to monitor this child
            $table->foreignId('staff_id')->nullable()->constrained('users')->nullOnDelete();
            // Parent linked to this child
            $table->foreignId('parent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('children');
    }
};
