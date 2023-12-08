<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Teacher;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teachers = [
            [
                'name' => 'أ. مؤمنة البواب',
                'branch_id' => 1,
            ],
            [
                'name' => 'أ. زاهر علوش',
                'branch_id' => 1,
            ],
            [
                'name' => 'أ. غفران مزيان',
                'branch_id' => 2,
            ],
            [
                'name' => 'أ. ايمان السمان',
                'branch_id' => 2,
            ],
        ];
        foreach ($teachers as $teacherData) {
            Teacher::create($teacherData);
        }
    }
}
