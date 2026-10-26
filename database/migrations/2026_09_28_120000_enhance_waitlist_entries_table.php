<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('waitlist_entries', function (Blueprint $table) {
            if (! Schema::hasColumn('waitlist_entries', 'date_from')) {
                $table->date('date_from')->nullable()->after('specialty');
            }
            if (! Schema::hasColumn('waitlist_entries', 'date_to')) {
                $table->date('date_to')->nullable()->after('date_from');
            }
            if (! Schema::hasColumn('waitlist_entries', 'time_from')) {
                $table->time('time_from')->nullable()->after('date_to');
            }
            if (! Schema::hasColumn('waitlist_entries', 'time_to')) {
                $table->time('time_to')->nullable()->after('time_from');
            }
            if (! Schema::hasColumn('waitlist_entries', 'modality')) {
                $table->string('modality', 20)->nullable()->after('time_to');
            }
            if (! Schema::hasColumn('waitlist_entries', 'offered_professional_id')) {
                $table->foreignId('offered_professional_id')->nullable()->after('notes')
                    ->constrained('professionals')->nullOnDelete();
            }
            if (! Schema::hasColumn('waitlist_entries', 'offered_date')) {
                $table->date('offered_date')->nullable()->after('offered_professional_id');
            }
            if (! Schema::hasColumn('waitlist_entries', 'offered_time')) {
                $table->time('offered_time')->nullable()->after('offered_date');
            }
            if (! Schema::hasColumn('waitlist_entries', 'offered_modality')) {
                $table->string('offered_modality', 20)->nullable()->after('offered_time');
            }
            if (! Schema::hasColumn('waitlist_entries', 'offered_at')) {
                $table->timestamp('offered_at')->nullable()->after('offered_modality');
            }
            if (! Schema::hasColumn('waitlist_entries', 'offer_expires_at')) {
                $table->timestamp('offer_expires_at')->nullable()->after('offered_at');
            }
            if (! Schema::hasColumn('waitlist_entries', 'confirmed_at')) {
                $table->timestamp('confirmed_at')->nullable()->after('offer_expires_at');
            }
            if (! Schema::hasColumn('waitlist_entries', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('confirmed_at');
            }
            if (! Schema::hasColumn('waitlist_entries', 'expired_at')) {
                $table->timestamp('expired_at')->nullable()->after('cancelled_at');
            }
            // assigned_at / assigned_appointment_id may already exist from previous migration
            if (! Schema::hasColumn('waitlist_entries', 'assigned_at')) {
                $table->timestamp('assigned_at')->nullable()->after('expired_at');
            }
            if (! Schema::hasColumn('waitlist_entries', 'assigned_appointment_id')) {
                $table->foreignId('assigned_appointment_id')->nullable()->after('assigned_at')
                    ->constrained('appointments')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('waitlist_entries', function (Blueprint $table) {
            $cols = [
                'date_from', 'date_to', 'time_from', 'time_to', 'modality',
                'offered_professional_id', 'offered_date', 'offered_time', 'offered_modality',
                'offered_at', 'offer_expires_at', 'confirmed_at', 'cancelled_at', 'expired_at',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('waitlist_entries', $col)) {
                    if ($col === 'offered_professional_id') {
                        $table->dropConstrainedForeignId('offered_professional_id');
                    } else {
                        $table->dropColumn($col);
                    }
                }
            }
        });
    }
};
