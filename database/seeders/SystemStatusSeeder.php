<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemStatusSeeder extends Seeder
{
    public function run()
    {
        if (DB::table('system_status')->count() === 0) {
            DB::table('system_status')->insert([
                'status'     => 'up',
                'reason'     => 'System initialized.',
                'changed_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}