<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    protected $table = "taxrates";
    use HasFactory;

    protected $fillable = [
        'taxrate',
    ];
}
