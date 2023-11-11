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
                'name' => 'أ. مؤمنة بواب',
                'branch_id' => 1,
            ],
            [
                'name' => 'أ. زاهر علوش',
                'branch_id' => 1,
            ],
            [
                'name' => 'أ. غفران مزيان',
                'branch_id' => 1,
            ],
            [
                'name' => 'أ. ايمان السمان',
                'branch_id' => 1,
            ],
        ];
        foreach ($teachers as $teacherData) {
            Teacher::create($teacherData);
        }
    }
}
