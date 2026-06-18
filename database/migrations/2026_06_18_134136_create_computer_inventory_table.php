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
        Schema::create('computer_inventory', function (Blueprint $table) {
            $table->id();
            $table->string('item_number', 50)->nullable();
            $table->string('property_no')->nullable();
            $table->date('purchase_date')->nullable();
            $table->string('article', 100)->nullable();
            $table->string('computer_set_description', 200);
            $table->text('description')->nullable();
            $table->string('brand', 100)->nullable();
            $table->string('processor', 100);
            $table->string('ram', 50);
            $table->string('storage', 100);
            $table->enum('unit', ['unit', 'box', 'pcs', 'lot'])->default('unit');
            $table->enum('device_type', ['Desktop', 'Laptop', 'All-in-One']);
            $table->enum('keyboard_status', ['OK', 'Missing', 'Damaged', 'Needs Repair'])->default('OK');
            $table->enum('mouse_status', ['OK', 'Missing', 'Damaged', 'Needs Repair'])->default('OK');
            $table->enum('power_cord_status', ['OK', 'Missing', 'Damaged', 'Needs Repair'])->default('OK');
            $table->enum('hdmi_status', ['OK', 'Missing', 'Damaged', 'Needs Repair'])->default('OK');
            $table->string('operating_system', 100)->nullable();
            $table->string('serial_number', 200);
            $table->string('serial_number_monitor', 200)->nullable();
            $table->string('serial_number_system', 200)->nullable();
            $table->enum('condition_status', ['Excellent', 'Good', 'Fair', 'Poor', 'Damaged'])->default('Good');
            $table->foreignId('location_id')->nullable()->constrained('locations')->onDelete('set null');
            $table->foreignId('campus_id')->nullable()->constrained('campuses')->onDelete('set null');
            $table->text('remarks')->nullable();
            $table->string('image_path')->nullable();
            $table->enum('status', ['available', 'assigned', 'maintenance', 'damaged', 'retired'])->default('available');
            $table->boolean('is_condemned')->default(false);
            $table->timestamp('condemned_date')->nullable();
            $table->text('condemned_reason')->nullable();
            $table->foreignId('condemned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('assigned_date')->nullable();
            $table->decimal('cost', 10, 2)->default(0);
            $table->timestamps();

            $table->index('location_id', 'idx_computer_inventory_location');
            $table->index('assigned_to', 'idx_computer_inventory_assigned');
            $table->index('status', 'idx_computer_inventory_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('computer_inventory');
    }
};
