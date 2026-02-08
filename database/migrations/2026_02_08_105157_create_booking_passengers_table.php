<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enum\PassengerTypeEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('booking_passengers', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('booking_item_id')
                ->index()
                ->constrained('booking_items')
                ->cascadeOnDelete();
            
            $table->string('full_name')->index();
            $table->date('dob')->nullable();
            $table->string('gender')->nullable();
            $table->string('phone')->nullable()->index();
            
            $table->string('passport_number')->nullable()->index();
            $table->date('passport_expiry')->nullable();
            $table->string('nationality')->nullable();

            $table->string('type')->default(PassengerTypeEnum::Adult->value);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_passengers');
    }
};
