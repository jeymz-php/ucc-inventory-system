<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@ucc-caloocan.edu.ph'],
            [
                'name'          => 'System Administrator',
                'email'         => 'admin@ucc-caloocan.edu.ph',
                'password'      => Hash::make('Admin@1234'),
                'role'          => 'superadmin',
                'is_active'     => true,
                'campus_id'     => null,
                'department_id' => null,
                'phone'         => null,
            ]
        );
    }
}