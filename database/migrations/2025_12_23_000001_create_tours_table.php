<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('location');
            $table->unsignedInteger('duration_days');
            $table->unsignedInteger('base_price_bdt');
            $table->string('hero_image_url');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured_ongoing')->default(false);
            $table->date('next_start_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};

