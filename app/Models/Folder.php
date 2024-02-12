<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;
    protected $fillable = [ 'name ' , 'start_page', 'end_page'];

    public function version()
    {
        return $this->belongsTo(Version::class);
    }
    public function students()
    {
        return $this->hasMany(Student::class);
    }
    public function tests()
    {
        return $this->hasMany(Test::class);
    }
}
