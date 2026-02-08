<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enum\BlogStatusEnum;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('blogs');
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable(); 
            $table->longText('content'); 
            $table->string('image')->nullable(); 
            $table->unsignedBigInteger('blog_category_id')->nullable()->index(); 
            $table->foreignId('author_id')
                ->index()
                ->constrained('admins') 
                ->cascadeOnDelete();
            $table->string('status')->default(BlogStatusEnum::Draft->value)->index();
            $table->tinyInteger('is_featured')->default(0)->index(); 
            
            $table->integer('view_count')->default(0)->index(); 
            $table->timestamp('published_at')->nullable()->index();

            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();

            $table->timestamps(); 
            $table->softDeletes();

            $table->fullText(['name', 'content']); 
        });

        Schema::table('blogs', function (Blueprint $table) {
            $table->foreign('blog_category_id')
                ->references('blog_category_id')
                ->on('blog_categories')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};