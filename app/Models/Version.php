<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Version extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'no_pages'];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }


    public function folders()
    {
        return $this->hasMany(Folder::class);
    }
}
