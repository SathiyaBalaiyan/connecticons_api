<?php

namespace App\Http\Controllers\Methods;
// use Illuminate\Http\Request;

use Response;
use Exception;
use App\Models\Webtheme;
use App\Models\Milestone;
use App\Models\PurchaseOrder;
use App\Models\Vendors as User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

// Models
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;



class DbactionsController extends ConfigController
    // class MethodsCollection extends DbactionsController
{

    public function __construct()
    {
        
    }

    // =========================================> Exists or Not  <=============================
    //-----  Invoice exists or not
    public function invoiceExists($vendorInvoiceNo)
    {
        return DB::table('invoices')->where('vendor_invoice_no', $vendorInvoiceNo)->exists();
    }
    //-----  Vendor using id

    public function vendorExists($vendorId)
    {
        return User::where('id', $vendorId)->first()->exists();
    }
    //----  Vendor  using email
    public function getUserWithEmail($email)
    {
        $queryResult = User::where('email', $email)->first();

        return $queryResult;
    }
    //-------- Given id is admin?
    public function isAdmin($loginUserId)
    {
        // return User::where('id', $loginUserId)->where("role_id", 1)->first()->exists();
        return User::where('id', $loginUserId)
            ->where('role_id', 1)
            ->exists();
    }
    //------- get theme number for given user

    public function getThemeNumber($userId)
    {
        $queryResult = Webtheme::where('user_id', $userId)->first();
        if ($queryResult) {
            return intval(0 < $queryResult->theme_number && $queryResult->theme_number < 8 ? $queryResult->theme_number : 1);
        } else {
            return 1;
        }
    }

    // checking for is exists purchase order number

    public function isExistsPONumber($poNumber)
    {
        return PurchaseOrder::where('po_number', $poNumber)->exists();
    }
    // checking for is exists purchase order id
    public function isExistsPoId($poId)
    {
        return PurchaseOrder::where('id', $poId)->exists();
    }

    //  checking for is exists milestone id
    public function isExistsMilestoneId($milestoneId)
    {
        return Milestone::where('id', $milestoneId)->exists();
    }


    // =============================================> Role Checking   <=======================

    public function isRequester($loginUserId)
    {
        // return User::where('id', $vendorId)->where("role_id", $roleId)->orWhere("role_id", $roleId)->first()->exists();
        return User::where('id', $loginUserId)
            ->where(function ($query) {
                $query->where('role_id', 4)
                    ->orWhere('role_id', 2);
            })->exists();
    }




}