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
        Schema::create('consumable_stock_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consumable_id')->constrained()->onDelete('cascade');
            $table->enum('action', ['initial', 'refill', 'deduction', 'adjustment']);
            $table->integer('change_amount');
            $table->integer('previous_total');
            $table->integer('new_total');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
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
        Schema::dropIfExists('consumable_stock_logs');
    }
};
