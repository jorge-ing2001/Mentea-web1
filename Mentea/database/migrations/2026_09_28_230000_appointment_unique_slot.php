<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Evita doble cita mismo profesional + fecha + hora (idempotencia)
            $table->unique(
                ['professional_id', 'date', 'time'],
                'appointments_professional_date_time_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropUnique('appointments_professional_date_time_unique');
        });
    }
};
