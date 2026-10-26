<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('schedule_exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_id')->nullable()->constrained()->cascadeOnDelete();
            // null professional_id = festivo / bloqueo global clínica
            $table->date('date');
            $table->time('time_from')->nullable(); // null = día completo
            $table->time('time_to')->nullable();
            $table->string('type', 30)->default('block'); // block | holiday | absence
            $table->string('reason')->nullable();
            $table->timestamps();
            $table->index(['professional_id', 'date']);
            $table->index(['date', 'type']);
        });

        Schema::table('surveys', function (Blueprint $table) {
            if (! Schema::hasColumn('surveys', 'reminder_sent_at')) {
                $table->timestamp('reminder_sent_at')->nullable();
            }
            if (! Schema::hasColumn('surveys', 'invited_at')) {
                $table->timestamp('invited_at')->nullable();
            }
        });

        Schema::table('appointments', function (Blueprint $table) {
            if (! Schema::hasColumn('appointments', 'survey_invited_at')) {
                $table->timestamp('survey_invited_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_exceptions');
    }
};
