<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('source', ['ims', 'cs'])->default('ims')->after('is_active');
            $table->enum('status', ['pending', 'active'])->default('active')->after('source');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['source', 'status']);
        });
    }
};