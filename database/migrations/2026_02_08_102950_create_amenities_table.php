<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enum\AmenityTypeEnum;
use App\Enum\ActiveStateEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('amenities', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->string('code')->unique();
            $table->string('icon')->nullable();
            
            $table->string('type')->default(AmenityTypeEnum::General->value)->index();
            
            $table->integer('position')->default(0);
            $table->integer('status')->default(ActiveStateEnum::Active->value)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amenities');
    }
};
