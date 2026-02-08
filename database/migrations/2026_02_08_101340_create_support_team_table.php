<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enum\SupportTeamGroupEnum;
use App\Enum\SupportTeamRoleEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('support_team', function (Blueprint $table) {
            $table->id();
            
            $table->string('name');
            $table->string('phone');
            $table->string('zalo')->nullable();
            $table->string('avatar')->nullable();
            
            $table->string('role')->default(SupportTeamRoleEnum::Consultant->value)->index(); 

            $table->string('group')->default(SupportTeamGroupEnum::General->value)->index(); 
            
            $table->integer('position')->default(0);
            $table->tinyInteger('status')->default(1)->index();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_team');
    }
};
