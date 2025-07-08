<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('damage_logs', function (Blueprint $table) {
        $table->id();
        $table->string('image_path');
        $table->integer('score');
        $table->string('level'); // Minor, Moderate, Severe
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('damage_logs');
    }
};
