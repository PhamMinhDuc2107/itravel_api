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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('image');
            $table->string('mobile_image')->nullable();

            $table->string('link')->nullable();
            $table->string('target')->default('_self');
            $table->text('description')->nullable();

            $table->string('type')->default('home_slider')->index();
            $table->integer('position')->default(0);

            $table->tinyInteger('status')->default(1)->index();
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
