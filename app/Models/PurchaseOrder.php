<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use function PHPSTORM_META\map;

class PurchaseOrder extends Model
{

    protected $table = "purchase_orders";

    use HasFactory;

    protected $fillable = [
        'requester_id',
        "po_number",
        "line_item_description",
        "quantity",
        "rate",
        "amount",
        "hsn",
        "tax_rate",
        "tax_amount",
        "file_path",
        'created_at',
        'updated_at'
    ];


    public function vendor()
    {
        return $this->belongsTo(Vendors::class, 'vendor_id');
    }

}


