<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'module', 'action', 'data', 'performed_by', 'performed_at'
    ];
}
