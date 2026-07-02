<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('system_status', function (Blueprint $table) {
            $table->enum('system', ['ims', 'cs'])->default('ims')->after('id');
        });

        // Seed one initial 'up' record for CS so the CS middleware always finds a row
        DB::table('system_status')->insert([
            'system'     => 'cs',
            'status'     => 'up',
            'reason'     => 'Initial CS system status.',
            'changed_by' => null,
            'changed_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        Schema::table('system_status', function (Blueprint $table) {
            $table->dropColumn('system');
        });
    }
};