<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refresh_tokens', function (Blueprint $table) {
            $table->id();

            $table->numericMorphs('tokenable');

            $table->string('user_agent', 500)->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->string('auth_type', 20);
            $table->string('token_hash', 64)->unique();

            $table->timestamp('expired_at');
            $table->timestamp('revoked_at')->nullable();

            $table->timestamps();

            $table->index(['tokenable_id', 'tokenable_type', 'auth_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refresh_tokens');
    }
};
