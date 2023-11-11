<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Book;
class BookSeeder extends Seeder
{
    public function run()
    {
        $books = [
            [
                'name' => 'رياض الصالحين',
                'no_exams' => 0,
            ],
            [
                'name' => 'صحيح البخاري ',
                'no_exams' => 35,
            ],
            [
                'name' => 'صحيح مسلم ',
                'no_exams' => 41,
            ],
            [
                'name' => 'صحيح الترمذي ',
                'no_exams' => 20,
            ],
            [
                'name' => ' سنن أبي داوود ',
                'no_exams' => 38,
            ],
            [
                'name' => ' سنن النسائي ',
                'no_exams' => 20,
            ],
            [
                'name' => ' سنن ابن ماجة ',
                'no_exams' => 22,
            ],
            [
                'name' => ' موطأ الإمام  مالك ',
                'no_exams' => 0,
            ],
        ];

       foreach ($books as $book) {
            Book::create($book);
        }
    }
}
