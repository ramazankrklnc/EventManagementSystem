<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('api_event_states', function (Blueprint $table) {
            $table->id();
            $table->string('event_id')->unique(); // API'dan gelen event ID
            $table->boolean('is_published')->default(true);
            $table->json('custom_data')->nullable(); // Özelleştirilmiş veriler için
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('api_event_states');
    }
}; 