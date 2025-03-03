<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('postcodes', function (Blueprint $table) {
            $table->id();
            $table->string('postcode');
            $table->geography('coordinates', 'point');
            // NOTE: I tried to use the spatial data but I had a lot of issues, I ended up storing both for now
            $table->decimal('long', 11, 8);
            $table->decimal('lat', 10, 8);
            $table->timestamps();

            $table->index('postcode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postcodes');
    }
};
