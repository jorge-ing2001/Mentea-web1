<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('professionals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('specialty');
            $table->string('profession')->nullable();
            $table->text('description')->nullable();
            $table->string('email')->unique();
            $table->string('phone', 30)->nullable();
            $table->string('photo')->nullable();
            $table->boolean('is_available')->default(true);
            $table->unsignedSmallInteger('appointment_duration_minutes')->default(45);
            $table->timestamps();
            $table->softDeletes();
            $table->index('specialty');
            $table->index('is_available');
        });
    }
    public function down(): void { Schema::dropIfExists('professionals'); }
};
