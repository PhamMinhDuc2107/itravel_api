<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enum\TourDepartureStatusEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tour_departures', function (Blueprint $table) {
            $table->id();
        
            $table->foreignId('tour_id')
                ->constrained('tours')
                ->cascadeOnDelete()
                ->index();
        
            $table->date('start_date');
        
            $table->decimal('price_adult', 12, 2)->nullable();
            $table->decimal('original_price_adult', 12, 2)->nullable();
        
            $table->decimal('price_child', 12, 2)->nullable();
            $table->decimal('original_price_child', 12, 2)->nullable();
        
            $table->decimal('price_infant', 12, 2)->nullable();
            $table->decimal('original_price_infant', 12, 2)->nullable();
        
            $table->integer('stock')->default(20);
            $table->integer('booked')->default(0);
        
            $table->string('status')
                ->default(TourDepartureStatusEnum::Available->value)
                ->index();
        
            $table->timestamps();
        
            // UNIQUE business rule
            $table->unique(['tour_id', 'start_date']);
        
            // Index theo query thực tế
            $table->index(['tour_id', 'start_date']);   // list lịch theo tour
            $table->index(['tour_id', 'status']);       // còn chỗ / hết chỗ
            $table->index(['status', 'start_date']);    // search tour sắp chạy
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_departures');
    }
};
