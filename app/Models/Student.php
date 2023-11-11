<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
        'photo',
        'branch_id',
        'key',
        'current_consistency',
        'max_consistency',
        'emoji',
        'current_folder_id',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function currentFolder()
    {
        return $this->belongsTo(Folder::class, 'current_folder_id');
    }
}
