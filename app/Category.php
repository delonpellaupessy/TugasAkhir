<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'photo', 'slug'
    ];

    protected $hidden = [

    ];
}
