<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_no', 20)->unique();
            $table->unsignedBigInteger('user_id');          // CS user who opened it
            $table->enum('type', ['chatbot', 'admin']);     // chatbot = request flow, admin = talk to admin
            $table->enum('status', ['open', 'closed', 'resolved'])->default('open');
            $table->string('subject', 200)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down() { Schema::dropIfExists('conversations'); }
};