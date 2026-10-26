<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            if (! Schema::hasColumn('support_tickets', 'type')) {
                $table->string('type', 30)->default('support')->after('user_id')
                    ->comment('support = solicitud usuario; platform_event = diligenciamiento admin');
            }
            if (! Schema::hasColumn('support_tickets', 'category')) {
                $table->string('category', 50)->nullable()->after('type');
            }
            if (! Schema::hasColumn('support_tickets', 'event_date')) {
                $table->timestamp('event_date')->nullable()->after('category');
            }
        });
    }

    public function down(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            foreach (['type', 'category', 'event_date'] as $col) {
                if (Schema::hasColumn('support_tickets', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
