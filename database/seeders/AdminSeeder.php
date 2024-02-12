<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSeeder extends Seeder
{
   
    public function run(): void
    {
        $admins = [
            [
                'name' => 'مديرة كلشي ',
                'password' => '1234567890',
                'role' => 'SuperAdmin' ,
            ],
            [
                'name' => 'مديرة الاموي ',
                'password' => '1234567890',
                'branch_id' => 1,
                'role' => 'Admin' ,

            ],
            [
                'name' => 'مديرة حسيبة ',
                'password' => '1234567890',
                'branch_id' => 2,
                'role' => 'Admin' ,

            ],
            [
                'name' => 'مديرة دير قانون  ',
                'password' => '1234567890',
                'branch_id' => 3,
                'role' => 'Admin' ,

            ],
        ];
        foreach ($admins as $admin) {
            User::create($admin);
        }
    }
}
