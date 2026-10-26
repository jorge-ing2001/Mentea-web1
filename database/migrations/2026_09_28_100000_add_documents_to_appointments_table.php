<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('order_document')->nullable()->after('notes');
            $table->string('authorization_document')->nullable()->after('order_document');
        });

        Schema::table('waitlist_entries', function (Blueprint $table) {
            $table->timestamp('assigned_at')->nullable()->after('notes');
            $table->foreignId('assigned_appointment_id')->nullable()->after('assigned_at')
                ->constrained('appointments')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('waitlist_entries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('assigned_appointment_id');
            $table->dropColumn('assigned_at');
        });
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['order_document', 'authorization_document']);
        });
    }
};
