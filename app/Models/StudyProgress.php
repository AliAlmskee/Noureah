<?php

// app/Models/StudyProgress.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'folder_id',
        'finished',
    ];

    protected $casts = [
        'finished' => 'string',
    ];

    protected $attributes = [
        'finished' => '0',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }
}
