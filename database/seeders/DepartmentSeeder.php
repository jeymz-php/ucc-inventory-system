<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $departments = [
            ['department_name' => 'Admin Office',                       'description' => 'Administration Office'],
            ['department_name' => 'Graduate School Office',             'description' => 'Graduate School Office'],
            ['department_name' => 'Office of the President',            'description' => 'Office of the President'],
            ['department_name' => 'Accounting and Finance',             'description' => 'Accounting and Finance Department'],
            ['department_name' => 'HR',                                 'description' => 'Human Resources'],
            ['department_name' => 'Guidance and Counseling',            'description' => 'Guidance and Counseling Office'],
            ['department_name' => 'CSD',                                'description' => 'Computer Studies Department'],
            ['department_name' => 'MIS',                                'description' => 'Management Information Systems'],
            ['department_name' => 'Dean\'s Office',                     'description' => 'Dean\'s Office'],
            ['department_name' => 'CLAS Coordinator Office',            'description' => 'CLAS Coordinator Office'],
            ['department_name' => 'CBA Coordinator Office',             'description' => 'CBA Coordinator Office'],
            ['department_name' => 'OSA',                                'description' => 'Office of Student Affairs'],
            ['department_name' => 'IT Center',                          'description' => 'Information Technology Center'],
            ['department_name' => 'LabTech',                            'description' => 'Laboratory Technician'],
            ['department_name' => 'College of Law Office',              'description' => 'College of Law Office'],
            ['department_name' => 'Quality Assurance Office',           'description' => 'Quality Assurance Office'],
            ['department_name' => 'Research Office',                    'description' => 'Research Office'],
            ['department_name' => 'CCJE Office',                        'description' => 'College of Criminal Justice Education'],
            ['department_name' => 'NSTP/ROTC Office',                   'description' => 'NSTP/ROTC Office'],
            ['department_name' => 'P.E Department Office',              'description' => 'Physical Education Department'],
            ['department_name' => 'Registrar',                          'description' => 'Registrar\'s Office'],
            ['department_name' => 'Library',                            'description' => 'Library Office'],
            ['department_name' => 'Graduate School',                    'description' => 'Graduate School Office'],
            ['department_name' => 'Academic Affairs',                   'description' => 'Academic Affairs Office'],
            ['department_name' => 'GSD',                                'description' => 'General Services Department'],
            ['department_name' => 'Human Resources Mgt. Dept.',         'description' => 'Human Resources Management Department'],
            ['department_name' => 'Planning Office',                    'description' => 'Planning Office'],
            ['department_name' => 'Extension Services Dept.',           'description' => 'Extension Services Department'],
            ['department_name' => 'Employability Office',               'description' => 'Employability Office'],
            ['department_name' => 'Alumni Office',                      'description' => 'Alumni Office'],
            ['department_name' => 'Scholarship and Grants Office',      'description' => 'Scholarship and Grants Office'],
            ['department_name' => 'Student Affairs and Services Office', 'description' => 'Student Affairs and Services Office'],
            ['department_name' => 'COE',                                'description' => 'College of Education'],
            ['department_name' => 'College of Law',                     'description' => 'College of Law'],
            ['department_name' => 'CBA',                                'description' => 'College of Business and Accountancy'],
            ['department_name' => 'CLAS',                               'description' => 'College of Liberal Arts and Sciences'],
            ['department_name' => 'CCJE',                               'description' => 'College of Criminal Justice Education'],
            ['department_name' => 'Clinic',                             'description' => 'University Clinic'],
            ['department_name' => 'FMSO',                               'description' => 'FMSO'],
            ['department_name' => 'GAD Office',                         'description' => 'GAD Office'],
        ];

        foreach ($departments as $dept) {
            DB::table('departments')->insertOrIgnore([
                'department_name' => $dept['department_name'],
                'description'     => $dept['description'],
                'is_active'       => 1,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }
    }
}