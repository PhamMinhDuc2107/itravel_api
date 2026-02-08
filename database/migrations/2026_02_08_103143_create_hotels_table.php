<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enum\ActiveStateEnum;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            
            $table->string('name')->index();
            $table->string('slug')->unique();
            
            $table->foreignId('hotel_type_id')->constrained('hotel_types');
            $table->unsignedBigInteger('location_id')->index();
            
            $table->string('address')->nullable();
            
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            $table->integer('star_rating')->default(0)->index();
            $table->decimal('price_from', 12, 2)->default(0)->index();
            
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->text('policies')->nullable();
            
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            
            $table->string('check_in_time')->default('14:00');
            $table->string('check_out_time')->default('12:00');

            $table->tinyInteger('is_featured')->default(0)->index();
            $table->integer('view_count')->default(0);
            $table->integer('status')->default(ActiveStateEnum::Active->value)->index();
            
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            
            $table->timestamps();
            $table->softDeletes()->index();
        });

        Schema::table('hotels', function (Blueprint $table) {
            $table->foreign('location_id')
                ->references('location_id')
                ->on('locations')
                ->cascadeOnDelete();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
