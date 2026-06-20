<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        foreach (['computer_inventory', 'general_equipment', 'kitchen_equipment', 'lab_equipment', 'office_equipment'] as $t) {
            Schema::table($t, function (Blueprint $table) use ($t) {
                if (!Schema::hasColumn($t, 'is_wasted')) {
                    $table->boolean('is_wasted')->default(false)->after('is_condemned');
                }
                if (!Schema::hasColumn($t, 'wasted_date')) {
                    $table->timestamp('wasted_date')->nullable()->after('is_wasted');
                }
                if (!Schema::hasColumn($t, 'wasted_by')) {
                    $table->foreignId('wasted_by')->nullable()->after('wasted_date')->constrained('users')->onDelete('set null');
                }
            });
        }
    }

    public function down()
    {
        foreach (['computer_inventory', 'general_equipment', 'kitchen_equipment', 'lab_equipment', 'office_equipment'] as $t) {
            Schema::table($t, function (Blueprint $table) {
                $table->dropColumn(['is_wasted', 'wasted_date', 'wasted_by']);
            });
        }
    }
};