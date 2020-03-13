<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    // Fillable properties for Message model
    protected $fillable =[
        'title',
        'description',
        'author',
        'message_file',
        'message_picture'
    ];
}
