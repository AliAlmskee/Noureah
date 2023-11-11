<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = [
            [
                "name" => "المقر الرئيسي / الأموي"
            ],
            [
                "name" => "مقر حسيبة"
            ],
            [
                "name" => "مقر دير قانون"
            ],
            [
                "name" => "غير ذلك"
            ]
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}
