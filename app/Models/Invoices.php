<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoices extends Model
{
    protected $table = "invoices";
    use HasFactory;
    protected $fillable = [
        'vendor_id',
        'vendor_invoice_no',
        'currency',
        'gross_amount',
        'tax_amount',
        'taxable_value',

        'business_place',
        'tax_rate',
        'invoice_date',
        'vendor_code',
        'vendor_gst_no',

        'gst_no',
        'pos',
        'tax_type',
        'requester_name',
        'lut_number',

        'lut_expire_date',
        'eway_bill_no',
        'reason_delayed_submission',
        'tcs',
        'vendor_type',
        'vendor_remarks',
        'client_remarks',
        'invoice_image'
        // 'created_at',
        // 'updated_at'
    ];




}
