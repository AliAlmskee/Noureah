<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            [
                "name" => "المقر الرئيسي / الأموي",
                "season_start" => "2024-02-01",
                "season_end" => "2024-02-28",
            ],
            [
                "name" => "مقر حسيبة",
                "season_start" => "2024-02-01",
                "season_end" => "2024-02-28",
            ],
            [
                "name" => "مقر دير قانون",
                "season_start" => "2024-02-01",
                "season_end" => "2024-02-28",
            ],
            [
                "name" => "أونلاين",
                "season_start" => "2024-02-01",
                "season_end" => "2024-02-28",
            ],
        ];
    
        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}
