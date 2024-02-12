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
        'previous_consistency',
        'current_consistency',
        'max_consistency',
        'current_folder_id',
        'days_inrow',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function currentFolder()
    {
        return $this->belongsTo(Folder::class, 'current_folder_id');
    }

    public function tests()
    {
        return $this->hasMany(Test::class);
    }
    
}
