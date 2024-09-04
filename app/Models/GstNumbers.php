<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GstNumbers extends Model
{
    use HasFactory;
    protected $table = "gst_numbers";


    protected $fillable = [    
        'vendor_gst_no'
    ];

}