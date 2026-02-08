<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('blog_categories');
        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index(); 
            $table->string('slug')->unique(); 
            $table->text('description')->nullable();
            $table->integer('position')->default(0)->index();
            $table->string('status')->default('active')->index(); 
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->timestamps();
            $table->softDeletes()->index(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_categories');
    }
};