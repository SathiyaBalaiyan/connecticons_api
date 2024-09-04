<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorGstNumber extends Model
{
    protected $table = "vendor_gst_numbers";
    use HasFactory;
    
    protected $fillable = [
      
        'vendor_gst_no'
    ];
}
