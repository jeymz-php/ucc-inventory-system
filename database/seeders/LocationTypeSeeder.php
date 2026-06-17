<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Campus;

class LocationTypeSeeder extends Seeder
{
    public function run()
    {
        $mainCampus = Campus::where('code', 'MAIN')->first();
        if (!$mainCampus) return;

        $types = [
            ['id' => 25, 'type_code' => 'Ground Floor', 'type_name' => 'Admin Building', 'description' => 'Houses the university\'s executive and academic management offices', 'icon_class' => 'fa-briefcase'],
            ['id' => 26, 'type_code' => 'Ground Floor', 'type_name' => 'Student Services & Academic Hub', 'description' => 'Serves as the central hub for student services, health, safety, and specialized academic departments', 'icon_class' => 'fa-briefcase'],
            ['id' => 27, 'type_code' => '1st Floor', 'type_name' => 'Academic and Laboratory Complex', 'description' => 'Houses lecture rooms, specialized laboratories, and administrative offices for academic and student services', 'icon_class' => 'fa-desktop'],
            ['id' => 28, 'type_code' => '3rd Floor', 'type_name' => 'Technology and Student Services Floor', 'description' => 'Houses computer laboratories, multimedia facilities, student support offices, and administrative coordinator rooms.', 'icon_class' => 'fa-desktop'],
            ['id' => 29, 'type_code' => '2nd Floor', 'type_name' => 'Business, Law, and Hospitality Floor', 'description' => 'Houses lecture rooms and training facilities for business administration, law, and hospitality management programs.', 'icon_class' => 'fa-chalkboard-teacher'],
            ['id' => 30, 'type_code' => '4th Floor', 'type_name' => 'Laboratories and Academic Instruction Floor', 'description' => 'Houses science laboratories, educational technology facilities, simulation rooms, and lecture rooms for academic instruction.', 'icon_class' => 'fa-flask'],
            ['id' => 31, 'type_code' => '5th Floor', 'type_name' => 'Behavioral Sciences and Events Floor', 'description' => 'Houses psychology department facilities, social hall for campus events, and lecture rooms.', 'icon_class' => 'fa-desktop'],
        ];

        foreach ($types as $type) {
            DB::table('location_types')->insertOrIgnore([
                'id'              => $type['id'],
                'type_code'       => $type['type_code'],
                'type_name'       => $type['type_name'],
                'campus_id'       => $mainCampus->id,
                'description'     => $type['description'],
                'icon_class'      => $type['icon_class'],
                'color_primary'   => '#1a6b3a',
                'color_secondary' => '#20c997',
                'equipment_label' => 'Equipment',
                'manager_title'   => 'Manager',
                'is_active'       => 1,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }
    }
}