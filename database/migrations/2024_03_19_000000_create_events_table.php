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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('type'); // concert, sports, theatre, exhibition
            $table->dateTime('event_date');
            $table->string('location');
            $table->decimal('price', 10, 2);
            $table->integer('available_tickets');
            $table->string('image_url')->nullable();
            $table->boolean('weather_dependent')->default(false);
            $table->json('categories')->nullable(); // For user interests/recommendations
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
}; 