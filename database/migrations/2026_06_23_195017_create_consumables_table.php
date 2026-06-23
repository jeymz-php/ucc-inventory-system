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
        Schema::create('consumables', function (Blueprint $table) {
            $table->id();
            $table->string('item_name', 200);
            $table->foreignId('category_id')->nullable()->constrained('consumable_categories')->onDelete('set null');
            $table->string('brand', 100)->nullable();
            $table->string('unit', 50); // pcs, box, gallon, etc.
            $table->integer('current_stock')->default(0);
            $table->integer('max_stock')->nullable();
            $table->integer('critical_threshold')->default(10);
            $table->integer('low_threshold')->default(20);
            $table->string('id_code', 50)->unique();
            $table->foreignId('campus_id')->nullable()->constrained()->onDelete('set null');
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
        Schema::dropIfExists('consumables');
    }
};
