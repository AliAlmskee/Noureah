<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookStudent extends Model
{
    use HasFactory;

    protected $table = 'book_student';

    protected $fillable = ['version_id' , 'student_id','is_open', 'percentage_finished', 'assigned_finished'];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
