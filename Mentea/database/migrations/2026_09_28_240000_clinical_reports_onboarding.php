<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'onboarding_completed_at')) {
                $table->timestamp('onboarding_completed_at')->nullable()->after('consent_data_at');
            }
            if (! Schema::hasColumn('users', 'preferred_theme')) {
                $table->string('preferred_theme', 10)->default('system')->after('onboarding_completed_at');
            }
        });

        Schema::table('appointments', function (Blueprint $table) {
            if (! Schema::hasColumn('appointments', 'confirm_token')) {
                $table->string('confirm_token', 64)->nullable()->unique();
            }
            if (! Schema::hasColumn('appointments', 'patient_confirmed_at')) {
                $table->timestamp('patient_confirmed_at')->nullable();
            }
            if (! Schema::hasColumn('appointments', 'diagnosis_code')) {
                $table->string('diagnosis_code', 20)->nullable();
            }
            if (! Schema::hasColumn('appointments', 'diagnosis_label')) {
                $table->string('diagnosis_label', 255)->nullable();
            }
        });

        if (! Schema::hasTable('clinical_evolutions')) {
            Schema::create('clinical_evolutions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('appointment_id')->constrained()->cascadeOnDelete();
                $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
                $table->text('content');
                $table->string('attachment_path')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('clinical_evolutions');
    }
};
