<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enum\LocationTypeEnum;
use App\Enum\ActiveStateEnum;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('locations');
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            
            $table->string('name')->index();
            $table->string('slug')->unique();
            
            $table->unsignedBigInteger('parent_id')->nullable()->index();

            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->string('image')->nullable();
            
            $table->string('type')
                ->default(LocationTypeEnum::Province->value)
                ->index()
                ->comment('country, region, province, attraction');


            $table->tinyInteger('display_home')->default(ActiveStateEnum::InActive->value)->index();
            $table->tinyInteger('is_feature')->default(ActiveStateEnum::InActive->value)->index();
            $table->tinyInteger('is_departure')->default(ActiveStateEnum::InActive->value)->index(); 
            $table->tinyInteger('is_destination')->default(ActiveStateEnum::Active->value)->index(); 
            
            $table->integer('position')->default(0)->index();
            
            $table->string('status')->default(ActiveStateEnum::Active->value)->index();

            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();

            $table->timestamps();
            $table->softDeletes()->index();
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->foreign('parent_id')
                ->references('id')
                ->on('locations')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
