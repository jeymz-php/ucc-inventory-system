<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EquipmentArticleSeeder extends Seeder
{
    public function run()
    {
        $articles = [
            // Computer
            ['equipment_type' => 'Computer', 'name' => 'Desktop'],
            ['equipment_type' => 'Computer', 'name' => 'Laptop'],
            ['equipment_type' => 'Computer', 'name' => 'All-in-One'],
            ['equipment_type' => 'Computer', 'name' => 'Computer Package'],
            // Kitchen
            ['equipment_type' => 'Kitchen', 'name' => 'Refrigerator'],
            ['equipment_type' => 'Kitchen', 'name' => 'Stove'],
            ['equipment_type' => 'Kitchen', 'name' => 'Oven'],
            ['equipment_type' => 'Kitchen', 'name' => 'Microwave'],
            // Office
            ['equipment_type' => 'Office', 'name' => 'Desk'],
            ['equipment_type' => 'Office', 'name' => 'Chair'],
            ['equipment_type' => 'Office', 'name' => 'Filing Cabinet'],
            ['equipment_type' => 'Office', 'name' => 'Printer'],
            ['equipment_type' => 'Office', 'name' => 'Projector'],
            // Lab
            ['equipment_type' => 'Lab', 'name' => 'Microscope'],
            ['equipment_type' => 'Lab', 'name' => 'Centrifuge'],
            ['equipment_type' => 'Lab', 'name' => 'Bunsen Burner'],
            // General
            ['equipment_type' => 'General', 'name' => 'Whiteboard'],
            ['equipment_type' => 'General', 'name' => 'Aircon'],
            ['equipment_type' => 'General', 'name' => 'Electric Fan'],
        ];

        foreach ($articles as $a) {
            DB::table('equipment_articles')->insertOrIgnore([
                'equipment_type' => $a['equipment_type'],
                'name'           => $a['name'],
                'is_active'      => 1,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }
    }
}