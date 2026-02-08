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
        Schema::create('booking_items', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('booking_id')
                ->index()
                ->constrained('bookings')
                ->cascadeOnDelete();
            
            $table->string('productable_type')->nullable();
            $table->string('productable_id')->nullable();

            $table->string('product_name');
            $table->string('product_image')->nullable();
            $table->string('product_code')->nullable()->index();

            $table->integer('quantity')->default(1);
            $table->decimal('price', 15, 2);
            $table->decimal('total_price', 15, 2);

            $table->dateTime('start_date')->nullable()->index(); 
            $table->dateTime('end_date')->nullable()->index();

            $table->json('options')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_items');
    }
};
