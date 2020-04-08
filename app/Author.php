<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    //Author fillable fields
    protected $fillable = [
        'name',
        'phone',
        'email',
        'role'
    ];
}
