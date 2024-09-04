<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessPlaces extends Model
{
    protected $table = 'business_places';

    use HasFactory;
    protected $fillable = [
        'city',
        'state_id'
    ];
}
