<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kitchen_equipment', function (Blueprint $table) {
            $table->id();
            $table->string('item_number', 50)->nullable()->unique();
            $table->string('article')->nullable();
            $table->string('property_no')->nullable();
            $table->string('equipment_name');
            $table->text('description')->nullable();
            $table->string('brand', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->enum('unit', ['unit', 'box', 'pcs', 'lot'])->default('unit');
            $table->string('serial_number', 100)->nullable();
            $table->enum('condition_status', ['Excellent', 'Good', 'Fair', 'Poor', 'Damaged'])->default('Good');
            $table->foreignId('location_id')->nullable()->constrained('locations')->onDelete('set null');
            $table->foreignId('campus_id')->nullable()->constrained('campuses')->onDelete('set null');
            $table->enum('status', ['available', 'assigned', 'maintenance', 'condemned'])->default('available');
            $table->boolean('is_condemned')->default(false);
            $table->timestamp('condemned_date')->nullable();
            $table->text('condemned_reason')->nullable();
            $table->foreignId('condemned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('assigned_date')->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->text('remarks')->nullable();
            $table->string('image_path')->nullable();
            $table->decimal('cost', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kitchen_equipment');
    }
};
