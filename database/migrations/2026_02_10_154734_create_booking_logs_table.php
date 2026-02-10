<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->index()->constrained('bookings')->cascadeOnDelete();

            $table->foreignId('user_id')->nullable()->index()->constrained('users')->nullOnDelete();
            $table->string('causer_name')->nullable();

            $table->string('event')->index(); 

            $table->json('old_values')->nullable(); 
            $table->json('new_values')->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_logs');
    }
};
