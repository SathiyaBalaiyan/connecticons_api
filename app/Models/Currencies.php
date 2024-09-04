<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currencies extends Model
{
    protected $table = "currencies";
    use HasFactory;
    protected $fillable = [
        'country',
        'code',
        'currency',
        'symbol'
    ];
}

