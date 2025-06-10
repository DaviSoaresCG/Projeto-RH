<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    
        public function run(): void
    {
        // admin department
        $user_department = new Department();
        $user_department->name = 'Adminstração';
        $user_department->created_at = now();
        $user_department->updated_at = now();
        $user_department->save();

        // admin
        $user = new User();
        $user->name  = "Administrador";
        $user->email = "admin@gmail.com";
        $user->password = bcrypt("123");
        $user->role = 'admin';
        $user->permissions = '["admin"]';
        $user->created_at = now();
        $user->updated_at = now();
        $user->email_verified_at = now();
        $user->department_id = 1;
        $user->save();

        // admin details
        $user_details = new UserDetail();
        $user_details->user_id = 1;
        $user_details->address = 'Rua 1, do lado da Rua 2';
        $user_details->zip_code = '123-123';
        $user_details->city = 'Palmas';
        $user_details->phone = '4002-8922';
        $user_details->salary = 8000.00;
        $user_details->admission_date = '2025-06-07';
        $user_details->created_at = now();
        $user_details->updated_at = now();
        $user_details->save();

        // rh department
        $department = Department::create([
            'name' => 'Recursos Humanos'
        ]);

    }
    }

