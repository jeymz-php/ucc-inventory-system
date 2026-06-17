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
        Schema::create('location_types', function (Blueprint $table) {
            $table->id();
            $table->string('type_code', 50);
            $table->string('type_name', 100);
            $table->foreignId('campus_id')->constrained()->onDelete('cascade');
            $table->text('description')->nullable();
            $table->string('icon_class', 50)->default('fa-building');
            $table->string('color_primary', 7)->default('#1a6b3a');
            $table->string('color_secondary', 7)->default('#20c997');
            $table->string('equipment_label', 50)->default('Equipment');
            $table->string('manager_title', 50)->default('Manager');
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('location_types');
    }
};
