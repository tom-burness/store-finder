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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('status', ['open', 'closed']);
            $table->enum('type', ['takeaway', 'shop', 'restaurant']);
            $table->geography('coordinates', 'point');
            // NOTE: I tried to use the spatial data but I had a lot of issues, I ended up storing both for now
            $table->decimal('long', 11, 8);
            $table->decimal('lat', 10, 8);
            $table->integer('max_delivery_distance');
            $table->timestamps();

            $table->spatialIndex('coordinates');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
