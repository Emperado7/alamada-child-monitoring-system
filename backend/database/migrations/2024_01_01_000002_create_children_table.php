<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('children', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->date('date_of_birth');
            $table->string('gender');
            $table->string('address');
            $table->string('guardian_name');
            $table->string('guardian_contact');
            $table->string('diagnosis');
            $table->text('diagnosis_notes')->nullable();
            $table->string('photo')->nullable();
            $table->string('status')->default('active');
            $table->foreignId('staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE children ADD CONSTRAINT chk_children_gender CHECK (gender IN ('male','female'))");
        DB::statement("ALTER TABLE children ADD CONSTRAINT chk_children_diagnosis CHECK (diagnosis IN ('ADHD','Autism','Mental Disability','Cerebral Palsy','Blindness','Other'))");
        DB::statement("ALTER TABLE children ADD CONSTRAINT chk_children_status CHECK (status IN ('active','inactive','graduated'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('children');
    }
};
