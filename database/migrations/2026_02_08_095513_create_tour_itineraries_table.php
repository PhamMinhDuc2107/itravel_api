<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tour_itineraries', function (Blueprint $table) {
            $table->id();
        
            $table->foreignId('tour_id')
                ->constrained('tours')
                ->cascadeOnDelete();
        
            $table->integer('day_number');
            $table->integer('position')->default(0);
        
            $table->string('title');
            $table->longText('content');
        
            $table->timestamps();
            $table->softDeletes();
        
            $table->index(['tour_id', 'day_number']);
            $table->index(['tour_id', 'position']);
            $table->index('deleted_at');
            $table->fullText('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_itineraries');
    }
};
