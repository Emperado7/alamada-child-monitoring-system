<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Link a notification to the enrollment it is about (nullable for general notifications)
            $table->foreignId('enrollment_id')
                ->nullable()
                ->after('sent_at')
                ->constrained('enrollments')
                ->nullOnDelete();

            // Categorise the notification so clients can render contextual UI
            // e.g. 'enrollment_approved' | 'enrollment_rejected' | 'enrollment_pending' | 'general'
            $table->string('type', 50)
                ->default('general')
                ->after('enrollment_id');
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['enrollment_id']);
            $table->dropColumn(['enrollment_id', 'type']);
        });
    }
};
