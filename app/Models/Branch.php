<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = ['name','season_start','season_end'];

    public function teachers()
    {
        return $this->hasMany(Teacher::class);
    }
}
