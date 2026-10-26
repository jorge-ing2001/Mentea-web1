<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'document_type')) {
                $table->string('document_type', 20)->nullable()->after('document_number');
            }
            if (! Schema::hasColumn('users', 'eps')) {
                $table->string('eps', 120)->nullable()->after('phone');
            }
            if (! Schema::hasColumn('users', 'emergency_contact_name')) {
                $table->string('emergency_contact_name', 120)->nullable()->after('eps');
            }
            if (! Schema::hasColumn('users', 'emergency_contact_phone')) {
                $table->string('emergency_contact_phone', 30)->nullable()->after('emergency_contact_name');
            }
            if (! Schema::hasColumn('users', 'consent_informed_at')) {
                $table->timestamp('consent_informed_at')->nullable()->after('emergency_contact_phone');
            }
            if (! Schema::hasColumn('users', 'consent_data_at')) {
                $table->timestamp('consent_data_at')->nullable()->after('consent_informed_at');
            }
            if (! Schema::hasColumn('users', 'cancellation_count')) {
                $table->unsignedSmallInteger('cancellation_count')->default(0)->after('consent_data_at');
            }
            if (! Schema::hasColumn('users', 'no_show_count')) {
                $table->unsignedSmallInteger('no_show_count')->default(0)->after('cancellation_count');
            }
            if (! Schema::hasColumn('users', 'booking_blocked_until')) {
                $table->timestamp('booking_blocked_until')->nullable()->after('no_show_count');
            }
        });

        Schema::table('appointments', function (Blueprint $table) {
            if (! Schema::hasColumn('appointments', 'clinical_notes')) {
                $table->text('clinical_notes')->nullable()->after('notes');
            }
            if (! Schema::hasColumn('appointments', 'clinical_notes_updated_at')) {
                $table->timestamp('clinical_notes_updated_at')->nullable()->after('clinical_notes');
            }
            if (! Schema::hasColumn('appointments', 'clinical_notes_by')) {
                $table->foreignId('clinical_notes_by')->nullable()->after('clinical_notes_updated_at')
                    ->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('appointments', 'reminder_24h_sent_at')) {
                $table->timestamp('reminder_24h_sent_at')->nullable();
            }
            if (! Schema::hasColumn('appointments', 'reminder_2h_sent_at')) {
                $table->timestamp('reminder_2h_sent_at')->nullable();
            }
            if (! Schema::hasColumn('appointments', 'marked_no_show_at')) {
                $table->timestamp('marked_no_show_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['document_type','eps','emergency_contact_name','emergency_contact_phone',
                'consent_informed_at','consent_data_at','cancellation_count','no_show_count','booking_blocked_until'] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
        Schema::table('appointments', function (Blueprint $table) {
            foreach (['clinical_notes','clinical_notes_updated_at','clinical_notes_by',
                'reminder_24h_sent_at','reminder_2h_sent_at','marked_no_show_at'] as $col) {
                if (Schema::hasColumn('appointments', $col)) {
                    if ($col === 'clinical_notes_by') {
                        $table->dropConstrainedForeignId('clinical_notes_by');
                    } else {
                        $table->dropColumn($col);
                    }
                }
            }
        });
    }
};
