<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name',
        'password',
        'branch_id' ,
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
