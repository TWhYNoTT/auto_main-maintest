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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->bigIncrements('id');
            // Foreign keys for filters
            $table->unsignedBigInteger('vehicle_type_id')->nullable();
            $table->unsignedBigInteger('make_id')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('location_id')->nullable();
            // Vehicle-specific fields – these mirror data on Copart’s vehicle finder
            $table->string('vin', 20)->unique();
            $table->string('lot_number')->nullable();
            $table->string('model')->nullable();
            $table->integer('year')->nullable();
            $table->string('damage')->nullable();      // e.g. "Front End"
            $table->unsignedInteger('odometer')->nullable();
            $table->date('auction_date')->nullable();
            $table->text('description')->nullable();
            $table->json('images')->nullable();          // JSON array of image URLs

            // New columns
            $table->enum('odometer_status', ['Actual', 'Not Actual', 'Exempt'])->default('Actual');
            $table->decimal('estimated_retail_value', 10, 2)->nullable();
            $table->decimal('current_bid', 10, 2)->nullable();
            $table->enum('keys', ['Available', 'Not Available'])->default('Available');

            $table->timestamps();

            // Define foreign key constraints
            $table->foreign('vehicle_type_id')->references('id')->on('vehicle_types')->onDelete('set null');
            $table->foreign('make_id')->references('id')->on('makes')->onDelete('set null');
            $table->foreign('state_id')->references('id')->on('states')->onDelete('set null');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
