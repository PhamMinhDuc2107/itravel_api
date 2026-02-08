<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enum\TourStatusEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
        
            $table->string('code')->unique();
            $table->string('name')->index();
            $table->string('slug')->unique();
        
            $table->unsignedBigInteger('category_id')->nullable()->index();
            $table->unsignedBigInteger('departure_location_id')->index();
            $table->unsignedBigInteger('destination_location_id')->index();
        
            $table->integer('duration_days')->default(1);
            $table->integer('duration_nights')->default(0);
        
            $table->tinyInteger('is_recurring')->default(0)->index();
            $table->json('recurring_days')->nullable();
        
            $table->decimal('price_adult', 12, 2)->default(0);
            $table->decimal('price_child', 12, 2)->default(0);
            $table->decimal('price_infant', 12, 2)->default(0);
        
            $table->text('excerpt')->nullable();
            $table->longText('overview')->nullable();
            $table->longText('policy')->nullable();
            $table->text('included')->nullable();
            $table->text('excluded')->nullable();
        
            $table->string('image')->nullable();
            $table->json('gallery')->nullable();
        
            $table->integer('view_count')->default(0);
            $table->integer('position')->default(0)->index();
        
            $table->integer('status')
                ->default(TourStatusEnum::Published->value)
                ->index();
        
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
        
            $table->timestamps();
            $table->softDeletes()->index();
        
            $table->index(['status', 'category_id']);
            $table->index(['status', 'departure_location_id']);
            $table->index(['status', 'destination_location_id']);
            $table->index(['status', 'position']);
        });

        Schema::table('tours', function (Blueprint $table) {
            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->nullOnDelete();
            
            $table->foreign('departure_location_id')
                ->references('id')
                ->on('locations')
                ->cascadeOnDelete();
            
            $table->foreign('destination_location_id')
                ->references('id')
                ->on('locations')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
