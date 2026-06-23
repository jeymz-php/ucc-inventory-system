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
        Schema::create('consumable_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no', 30)->unique();
            $table->string('recipient_last_name', 100);
            $table->string('recipient_first_name', 100);
            $table->string('recipient_mi', 10)->nullable();
            $table->foreignId('campus_id')->nullable()->constrained()->onDelete('set null');
            $table->string('department', 150)->nullable();
            $table->date('request_date');
            $table->string('approved_by', 150)->nullable();
            $table->string('supply_officer', 150)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'partial'])->default('pending');
            $table->foreignId('requested_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
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
        Schema::dropIfExists('consumable_requests');
    }
};
