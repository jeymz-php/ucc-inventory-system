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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('location_name', 150);
            $table->foreignId('location_type_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('campus_id')->constrained()->onDelete('cascade');
            $table->text('description')->nullable();
            $table->integer('capacity')->default(0);
            $table->foreignId('facilitator_id')->nullable()->constrained('users')->onDelete('set null');
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
        Schema::dropIfExists('locations');
    }
};
