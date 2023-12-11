<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Version;

class VersionSeeder extends Seeder
{
    public function run()
    {
        $versions = [
            [
                'name' => 'مصطفى البغا',
                'no_pages' => 499,
                'book_id' => 1,
            ],
            [
                'name' => ' قاسم النوري -مصطفى الخن  ',
                'no_pages' => 578,
                'book_id' => 1,
            ],
            [
                'name' => 'محمود بيجو ',
                'no_pages' => 454,
                'book_id' => 1,
            ],
            [
                'name' => 'شعيب أرناؤؤط ',
                'no_pages' => 539,
                'book_id' => 1,
            ],


            // بخاري

            [
                'name' => 'مصطفى البغا',
                'no_pages' => 2585,
                'book_id' => 2,
            ],

            //مسلم

            [
                'name' => 'مصطفى البغا',
                'no_pages' => 2723,
                'book_id' => 3,
            ],



            //سنن الترمذي

            [
                'name' => 'أحمد شاكر',
                'no_pages' => 2454,
                'book_id' => 4 ,
            ],
            [
                'name' => 'محمود نصار',
                'no_pages' => 2311,
                'book_id' => 4,
            ],
            [
                'name' => 'عزت الدعاس',
                'no_pages' => 2629,
                'book_id' => 4,
            ],



            [
                'name' => 'محمد عوامة',
                'no_pages' => 2369,
                'book_id' => 5,
            ],
            [
                'name' => 'عبد القادر عبد الخير',
                'no_pages' => 3135,
                'book_id' => 5,
            ],


            [
                'name' => ' مكتب التراث الإسلامي',
                'no_pages' => 2454,
                'book_id' => 6,
            ],
            [
                'name' => 'محمد السيد _علي محمد علي',
                'no_pages' => 2903,
                'book_id' => 6,
            ],



            [
                'name' => 'خليل شيحا',
                'no_pages' => 2235,
                'book_id' => 7,
            ],

            [
                'name' => 'محمد فؤاد عبد الباقي',
                'no_pages' => 1600,
                'book_id' => 7,
            ],

            [
                'name' => 'احمد شمس الدين',
                'no_pages' => 2282,
                'book_id' => 7,
            ],


            [
                'name' => 'محمد صبحي حلاق',
                'no_pages' => 843,
                'book_id' => 8,
            ],
            [
                'name' => 'أبو عبد الرحمن الأخضري',
                'no_pages' => 890,
                'book_id' => 8,
            ],


        ];

        foreach ($versions as $version) {
            Version::create($version);
        }
    }
}
