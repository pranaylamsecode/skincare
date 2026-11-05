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
        // Add map location and service radius to specialists table
        Schema::table('specialists', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('pincode');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->unsignedInteger('service_radius_km')->default(10)->after('longitude');
        });

        // Create pincodes table for radius calculations
        Schema::create('pincodes', function (Blueprint $table) {
            $table->id();
            $table->string('pincode', 10)->index();
            $table->string('area_name');
            $table->string('city')->default('Ahmedabad');
            $table->string('state')->default('Gujarat');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->timestamps();

            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pincodes');

        Schema::table('specialists', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'service_radius_km']);
        });
    }
};
