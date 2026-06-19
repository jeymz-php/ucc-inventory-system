<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE computer_inventory MODIFY serial_number VARCHAR(200) NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE computer_inventory MODIFY serial_number VARCHAR(200) NOT NULL');
    }
};