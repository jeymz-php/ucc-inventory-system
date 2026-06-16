<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CampusSeeder extends Seeder
{
    public function run()
    {
        $campuses = [
            ['name' => 'Main Campus',                        'code' => 'MAIN'],
            ['name' => 'Congressional Extension Campus',     'code' => 'CONG'],
            ['name' => 'Camarin Extension Campus',           'code' => 'CAM'],
            ['name' => 'Bagong Silang Extension Campus',     'code' => 'BS'],
        ];

        foreach ($campuses as $campus) {
            DB::table('campuses')->insertOrIgnore([
                'name'       => $campus['name'],
                'code'       => $campus['code'],
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}