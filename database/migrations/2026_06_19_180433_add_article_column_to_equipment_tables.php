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
        foreach (['kitchen_equipment', 'office_equipment', 'lab_equipment', 'general_equipment'] as $t) {
            Schema::table($t, function (Blueprint $table) use ($t) {
                if (!Schema::hasColumn($t, 'article')) {
                    $table->string('article')->nullable()->after('id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('equipment_tables', function (Blueprint $table) {
            //
        });
    }
};
