<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enum\ConsultationStatusEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            
            $table->string('name');
            $table->string('phone')->index();
            $table->string('email')->nullable();
            $table->string('productable_type')->nullable();
            $table->string('productable_id')->nullable();
            $table->string('product_name')->nullable();
            $table->json('metadata')->nullable(); 
            $table->text('message')->nullable();
            $table->string('status')->default(ConsultationStatusEnum::Pending->value)->index(); 
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->text('staff_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
