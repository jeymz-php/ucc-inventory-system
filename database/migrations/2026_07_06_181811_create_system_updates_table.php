<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('system_updates', function (Blueprint $table) {
            $table->id();
            $table->string('version', 20);           // e.g. v2.1.0
            $table->string('title', 200);            // e.g. "July 2026 Update"
            $table->enum('system', ['ims', 'cs', 'both'])->default('both');
            $table->text('content');                 // update notes / improvements
            $table->boolean('show_modal')->default(true);  // show on login
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        // Seed initial version record
        DB::table('system_updates')->insert([
            'version'    => 'v1.0.0',
            'title'      => 'Initial Release',
            'system'     => 'both',
            'content'    => '• UCC Inventory Management System launched.\n• UCC Consumable Management System launched.\n• Role-based access control implemented.\n• Equipment and consumable tracking features released.',
            'show_modal' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('system_updates');
    }
};