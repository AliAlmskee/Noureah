<?php

// app/Models/Test.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'student_id',
        'folder_id',
        'no_mistakes',
        'no_pages',
        'time_in_minutes',
        'is_special',
        'mark',
        'pages',
        'emoji_id',
        'date',
        'color',
    ];

    protected $casts = [
        'pages' => 'array',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }
}
