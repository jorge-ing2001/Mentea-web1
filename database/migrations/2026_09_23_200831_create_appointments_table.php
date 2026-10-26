<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('professional_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('time');
            $table->string('modality', 20)->default('presencial');
            $table->string('status', 20)->default('pending');
            $table->text('reason')->nullable();
            $table->string('meet_link')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['patient_id', 'date']);
            $table->index(['professional_id', 'date']);
            $table->index(['status', 'date']);
            $table->unique(['professional_id', 'date', 'time', 'deleted_at'], 'unique_professional_slot');
        });
    }
    public function down(): void { Schema::dropIfExists('appointments'); }
};
