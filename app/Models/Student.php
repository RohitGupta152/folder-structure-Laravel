<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name', 'email', 'age', 'course', 'created_date', 'updated_date',
    ];
}
