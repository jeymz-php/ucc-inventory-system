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
        Schema::create('consumable_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consumable_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('consumable_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->string('purpose', 255)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
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
        Schema::dropIfExists('consumable_request_items');
    }
};
