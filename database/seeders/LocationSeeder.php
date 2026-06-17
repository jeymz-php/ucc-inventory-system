<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Campus;

class LocationSeeder extends Seeder
{
    public function run()
    {
        $mainCampus = Campus::where('code', 'MAIN')->first();
        if (!$mainCampus) return;

        $locations = [
            ['id' => 18, 'location_name' => 'Office of the University President', 'location_type_id' => 25, 'description' => 'Executive office for UCC system leadership and governance', 'capacity' => 300],
            ['id' => 19, 'location_name' => 'Office of the AA & QA', 'location_type_id' => 25, 'description' => 'Manages curriculum development and quality assurance', 'capacity' => 300],
            ['id' => 20, 'location_name' => 'Library', 'location_type_id' => 25, 'description' => 'Learning resource center for students and faculty.', 'capacity' => 200],
            ['id' => 21, 'location_name' => 'Library Office', 'location_type_id' => 25, 'description' => 'Staff area for library management and processing', 'capacity' => 100],
            ['id' => 22, 'location_name' => 'Discussion Room', 'location_type_id' => 25, 'description' => 'Collaborative study space for group activities', 'capacity' => 100],
            ['id' => 23, 'location_name' => 'Moot Court', 'location_type_id' => 25, 'description' => 'Courtroom simulation facility for legal education', 'capacity' => 50],
            ['id' => 24, 'location_name' => 'University Registrar', 'location_type_id' => 26, 'description' => 'Office managing student enrollment, records, and official transcripts', 'capacity' => 250],
            ['id' => 25, 'location_name' => 'Registrar\'s Office', 'location_type_id' => 26, 'description' => 'Workspace of the University Registrar and staff', 'capacity' => 100],
            ['id' => 26, 'location_name' => 'University Clinic', 'location_type_id' => 26, 'description' => 'On-campus health service for medical concerns', 'capacity' => 100],
            ['id' => 27, 'location_name' => 'Physical Education Department', 'location_type_id' => 26, 'description' => 'Handles physical education programs and athletic activities', 'capacity' => 100],
            ['id' => 28, 'location_name' => 'Criminology Department', 'location_type_id' => 26, 'description' => 'Home department for Criminology program instruction', 'capacity' => 100],
            ['id' => 29, 'location_name' => 'Security Post', 'location_type_id' => 26, 'description' => 'Central monitoring and control point for campus safety', 'capacity' => 100],
            ['id' => 30, 'location_name' => 'NSTP Office', 'location_type_id' => 27, 'description' => 'Handles National Service Training Program operations and activities', 'capacity' => 100],
            ['id' => 31, 'location_name' => 'Lecture Room 109', 'location_type_id' => 27, 'description' => 'General classroom for lectures and discussions', 'capacity' => 70],
            ['id' => 32, 'location_name' => 'Faculty Room', 'location_type_id' => 27, 'description' => 'Workspace for faculty members', 'capacity' => 70],
            ['id' => 33, 'location_name' => 'Storage Room A', 'location_type_id' => 27, 'description' => 'Storage for equipment and supplies', 'capacity' => 200],
            ['id' => 34, 'location_name' => 'Human Resource Office', 'location_type_id' => 27, 'description' => 'Manages personnel records and employee services', 'capacity' => 150],
            ['id' => 35, 'location_name' => 'Finance & Accounting Department', 'location_type_id' => 27, 'description' => 'Oversees administrative operations and financial management', 'capacity' => 350],
            ['id' => 36, 'location_name' => 'Photography Laboratory', 'location_type_id' => 27, 'description' => 'Facilitates forensic and crime scene photography training for criminology students', 'capacity' => 100],
            ['id' => 37, 'location_name' => 'Criminology Laboratory', 'location_type_id' => 27, 'description' => 'Laboratory for criminology experiments and simulations', 'capacity' => 150],
            ['id' => 38, 'location_name' => 'Lecture Room 101', 'location_type_id' => 27, 'description' => 'General classroom for lectures and discussions', 'capacity' => 70],
            ['id' => 39, 'location_name' => 'Kitchen Laboratory', 'location_type_id' => 27, 'description' => 'Hands-on facility for culinary training', 'capacity' => 150],
            ['id' => 40, 'location_name' => 'DTHIM Cold Kitchen', 'location_type_id' => 27, 'description' => 'Specialized lab for cold food preparation', 'capacity' => 150],
            ['id' => 41, 'location_name' => 'Bartending Laboratory', 'location_type_id' => 27, 'description' => 'Training area for bartending and mixology', 'capacity' => 100],
            ['id' => 42, 'location_name' => 'Research and Extension Office', 'location_type_id' => 27, 'description' => 'Manages research projects and community extension programs', 'capacity' => 100],
            ['id' => 43, 'location_name' => 'Lecture Room 108', 'location_type_id' => 27, 'description' => 'General classroom for lectures and discussions', 'capacity' => 70],
            ['id' => 44, 'location_name' => 'Lecture Room 106', 'location_type_id' => 27, 'description' => 'General classroom for lectures and discussions', 'capacity' => 70],
            ['id' => 45, 'location_name' => 'Lecture Room 104', 'location_type_id' => 27, 'description' => 'General classroom for lectures and discussions', 'capacity' => 70],
            ['id' => 46, 'location_name' => 'IT Center', 'location_type_id' => 27, 'description' => 'Computer laboratory for IT-related courses', 'capacity' => 50],
            ['id' => 47, 'location_name' => 'Guidance and Counseling Office', 'location_type_id' => 27, 'description' => 'Provides counseling and student support services', 'capacity' => 70],
            ['id' => 48, 'location_name' => 'Lab Tech Office', 'location_type_id' => 28, 'description' => '', 'capacity' => 150],
            ['id' => 49, 'location_name' => 'OVP for Administration', 'location_type_id' => 27, 'description' => 'Oversees and manages campus operations, facilities management, administrative services, and institutional support functions.', 'capacity' => 250],
            ['id' => 50, 'location_name' => 'BSBA HRM Training Room / College of Law Room 201', 'location_type_id' => 29, 'description' => '', 'capacity' => 150],
            ['id' => 51, 'location_name' => 'Lecture Room 204 / College of Law Room 202', 'location_type_id' => 29, 'description' => '', 'capacity' => 150],
            ['id' => 52, 'location_name' => 'Lecture Room 203', 'location_type_id' => 29, 'description' => '', 'capacity' => 100],
            ['id' => 53, 'location_name' => 'Lecture Room 205', 'location_type_id' => 29, 'description' => '', 'capacity' => 100],
            ['id' => 54, 'location_name' => 'Financial Management Room 206', 'location_type_id' => 29, 'description' => '', 'capacity' => 100],
            ['id' => 55, 'location_name' => 'Lecture Room 207', 'location_type_id' => 29, 'description' => '', 'capacity' => 100],
            ['id' => 56, 'location_name' => 'Financial Management Room 208', 'location_type_id' => 29, 'description' => '', 'capacity' => 100],
            ['id' => 57, 'location_name' => 'Lecture Room 209', 'location_type_id' => 29, 'description' => '', 'capacity' => 100],
            ['id' => 58, 'location_name' => 'Lecture Room 210', 'location_type_id' => 29, 'description' => '', 'capacity' => 100],
            ['id' => 59, 'location_name' => 'Lecture Room 211', 'location_type_id' => 29, 'description' => '', 'capacity' => 100],
            ['id' => 60, 'location_name' => 'Lecture Room 212', 'location_type_id' => 29, 'description' => '', 'capacity' => 100],
            ['id' => 61, 'location_name' => 'DTHIM FnB Room', 'location_type_id' => 29, 'description' => '', 'capacity' => 100],
            ['id' => 62, 'location_name' => 'Office of the Student Affairs Services', 'location_type_id' => 28, 'description' => '', 'capacity' => 150],
            ['id' => 63, 'location_name' => 'CBA Coordinator\'s Office', 'location_type_id' => 28, 'description' => '', 'capacity' => 150],
            ['id' => 64, 'location_name' => 'CBA Dean\'s Office', 'location_type_id' => 28, 'description' => '', 'capacity' => 150],
            ['id' => 65, 'location_name' => 'Storage Room B', 'location_type_id' => 28, 'description' => '', 'capacity' => 150],
            ['id' => 66, 'location_name' => 'MIS Data Center', 'location_type_id' => 28, 'description' => '', 'capacity' => 150],
            ['id' => 67, 'location_name' => 'Lecture Room 310', 'location_type_id' => 28, 'description' => '', 'capacity' => 100],
            ['id' => 68, 'location_name' => 'Lecture Room 308', 'location_type_id' => 28, 'description' => '', 'capacity' => 100],
            ['id' => 69, 'location_name' => 'CSD Department', 'location_type_id' => 28, 'description' => '', 'capacity' => 150],
            ['id' => 70, 'location_name' => 'Office of the CLAS Program Coordinator', 'location_type_id' => 28, 'description' => '', 'capacity' => 150],
            ['id' => 71, 'location_name' => 'Multimedia Room', 'location_type_id' => 28, 'description' => '', 'capacity' => 100],
            ['id' => 72, 'location_name' => 'Sound Engineering Laboratory', 'location_type_id' => 28, 'description' => '', 'capacity' => 100],
            ['id' => 73, 'location_name' => 'Computer Laboratory 1', 'location_type_id' => 28, 'description' => '', 'capacity' => 100],
            ['id' => 74, 'location_name' => 'Computer Laboratory 2', 'location_type_id' => 28, 'description' => '', 'capacity' => 100],
            ['id' => 75, 'location_name' => 'Lecture Room 304', 'location_type_id' => 28, 'description' => '', 'capacity' => 100],
            ['id' => 76, 'location_name' => 'Lecture Room 302', 'location_type_id' => 28, 'description' => '', 'capacity' => 100],
            ['id' => 77, 'location_name' => 'Computer Laboratory 3', 'location_type_id' => 28, 'description' => '', 'capacity' => 100],
            ['id' => 78, 'location_name' => 'Lecture Room 401', 'location_type_id' => 30, 'description' => '', 'capacity' => 100],
            ['id' => 79, 'location_name' => 'Lecture Room 402', 'location_type_id' => 30, 'description' => '', 'capacity' => 100],
            ['id' => 80, 'location_name' => 'College of Law Office', 'location_type_id' => 30, 'description' => '', 'capacity' => 150],
            ['id' => 81, 'location_name' => 'Lecture Room 404', 'location_type_id' => 30, 'description' => '', 'capacity' => 100],
            ['id' => 82, 'location_name' => 'Chemistry Laboratory', 'location_type_id' => 30, 'description' => '', 'capacity' => 150],
            ['id' => 83, 'location_name' => 'Physics Laboratory', 'location_type_id' => 30, 'description' => '', 'capacity' => 150],
            ['id' => 84, 'location_name' => 'Science Laboratory Supply Room', 'location_type_id' => 30, 'description' => '', 'capacity' => 150],
            ['id' => 85, 'location_name' => 'Biology Laboratory', 'location_type_id' => 30, 'description' => '', 'capacity' => 150],
            ['id' => 86, 'location_name' => 'Educational Technology Laboratory', 'location_type_id' => 30, 'description' => '', 'capacity' => 170],
            ['id' => 87, 'location_name' => 'Lecture Room 408', 'location_type_id' => 30, 'description' => '', 'capacity' => 100],
            ['id' => 88, 'location_name' => 'Early Childhood Simulation Room', 'location_type_id' => 30, 'description' => '', 'capacity' => 100],
            ['id' => 89, 'location_name' => 'Lecture Room 411', 'location_type_id' => 30, 'description' => '', 'capacity' => 100],
            ['id' => 90, 'location_name' => 'Storage Room C', 'location_type_id' => 30, 'description' => '', 'capacity' => 150],
            ['id' => 91, 'location_name' => 'Social Hall', 'location_type_id' => 31, 'description' => '', 'capacity' => 250],
            ['id' => 92, 'location_name' => 'Sound Engineering (Social Hall)', 'location_type_id' => 31, 'description' => '', 'capacity' => 50],
            ['id' => 93, 'location_name' => 'Lecture V-48', 'location_type_id' => 31, 'description' => '', 'capacity' => 100],
            ['id' => 94, 'location_name' => 'Lecture V-46', 'location_type_id' => 31, 'description' => '', 'capacity' => 100],
            ['id' => 95, 'location_name' => 'ROTC Department', 'location_type_id' => 26, 'description' => '', 'capacity' => 150],
        ];

        foreach ($locations as $loc) {
            DB::table('locations')->insertOrIgnore([
                'id'                => $loc['id'],
                'location_name'     => $loc['location_name'],
                'location_type_id'  => $loc['location_type_id'],
                'campus_id'         => $mainCampus->id,
                'description'       => $loc['description'],
                'capacity'          => $loc['capacity'],
                'facilitator_id'    => null,
                'is_active'         => 1,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }
}