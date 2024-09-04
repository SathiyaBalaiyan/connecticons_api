<?php

namespace App\Http\Controllers\Dropdown;


use Error;
use Response;

use Exception;
use App\Models\TaxRate;
use App\Models\Invoices;
use App\Models\Currencies;
use App\Models\GstNumbers;
use GuzzleHttp\Psr7\Message;


use Illuminate\Http\Request;

use App\Models\BusinessPlaces;
use App\Models\VendorGstNumber;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Methods\DbactionsController;

class DropdownController extends Controller
{

    private $dbactions;

    // Constructor method
    public function __construct()
    {
        $this->dbactions = new DbactionsController();
    }
    public function getRoles()
    {
        $data = DB::table("user_role")->select('id AS roleId', "role_name AS roleName")->get();
        return $this->dbactions->message("Data fetched successfully.", $data);
    }
    //Serch Data financial Year wise
    public function GetInvoiceWith1(Request $request)
    {

        $GSTNo = $this->dbactions->test_input($request->input("GSTNo") ?? "");
        // Retrieve the invoice ID from the request
        $invoice = Invoices::where("gst_no", intval($GSTNo))->first();
        // Return only the specified fields
        if (!isset($invoice) || empty($invoice)) {
            return $this->dbactions->errorMessage("no Gst data matched");
        }
        $data = [
            'atachedInvoice' => "Hyperlink to invoice",
            'invoiceNumber' => $invoice->vendor_invoice_no,
            'invoiceDate' => "1/1/2024",
            'submissionDate' => "1/1/2024",
            'grossAmount' => $invoice->gross_amount,

            'PONumber' => $invoice->pos,
            'requesterName' => "User Name",
            'receiptStatus' => "received",
            'GSTR2A' => "matched",
            'paymentStatus' => "success",
        ];

        return $this->dbactions->message("data fetched successfully", $data);

    }
    //  Demo datas for  Currencydatas
    public function getCurrencyDatas()
    {

        $currency = Currencies::select('id AS currencyId', 'country', 'code AS currencyCode')->get();

        return $this->dbactions->responseArray("data fetched successfully", $currency);

    }
    //  Demo datas for  BusinessPlaces
    public function getBusinessPlaces()
    {
        try {

            $businessPlaces = BusinessPlaces::select("city", "id", "state_id AS stateId")
                ->select('city', DB::raw('MIN(id) AS id'), DB::raw('MIN(state_id) AS stateId'))
                ->groupBy('city')
                ->orderBy('city')
                ->get();

            return $this->dbactions->responseArray("data fetched successfully", $businessPlaces);

        } catch (Exception $e) {
            return $this->dbactions->serverErrorMessage($e->getMessage());
        }
    }
    //  Demo datas for  taxRate
    public function getTaxRate()
    {
        try {

            $taxRates = TaxRate::select("id AS taxId", "taxrate AS taxRate")->get();

            return $this->dbactions->responseArray("data fetched successfully", $taxRates);

        } catch (Error | Exception $e) {
            return $this->dbactions->serverErrorMessage($e->getMessage());
        }
    }
    //  Demo datas for  Vendor GST numbers
    public function getVendorGst()
    {

        $vendorGST = VendorGstNumber::select('id AS vendorGSTId', 'vendor_gst_no AS vendorGSTNo')->get();

        return $this->dbactions->responseArray("data fetched successfully", $vendorGST);


    }
    //  Demo datas for  GST numbers
    public function getGstNumbers()
    {

        $GSTNos = GstNumbers::select('id AS gstId', 'gst_no AS GSTNo')->get();

        return $this->dbactions->responseArray("data fetched successfully", $GSTNos);

    }
    public function getPOS()
    {



        return $this->dbactions->message(
            "data fetched successfully",
            [
                ['id' => 1, 'data' => 'Demo1'],
                ['id' => 2, 'data' => 'Demo2'],
                ['id' => 3, 'data' => 'Demo3'],
                ['id' => 4, 'data' => 'Demo4'],
                ['id' => 5, 'data' => 'Demo5'],
                ['id' => 6, 'data' => 'Demo6'],
                ['id' => 7, 'data' => 'Demo7'],
                ['id' => 8, 'data' => 'Demo8'],
                ['id' => 9, 'data' => 'Demo9'],
                ['id' => 10, 'data' => 'Demo10'],

            ]
        );

    }
    public function getTaxTypes()
    {
        $resdata = array();
        $data = ['sales tax', 'service tax', 'on goods', 'excise duty', 'value added tax(VAT)', 'corporate tax', 'gst', 'other taxes'];
        sort($data);
        foreach ($data as $value) {
            static $i = 0;
            $resdata[] = ['id' => $i++, 'data' => $value];
        }
        return $this->dbactions->message(
            "data fetched successfully",
            // $data 
            $resdata
        );

    }
    public function reasonForDelayedSubmission()
    {
        $resdata = array();
        $data = ['not paid', 'paid after the EOD', 'rejected', 'personal injury', 'server error'];
        sort($data);
        foreach ($data as $value) {
            static $i = 0;
            $resdata[] = ['id' => $i++, 'data' => $value];
        }
        return $this->dbactions->message(
            "data fetched successfully",
            $resdata
        );

    }
    public function getVendorType()
    {
        $resdata = array();
        $data = ['Manufacturer', 'Wholesaler', 'Retailer', 'B2B', 'B2G', 'B2C', 'Service Provider'];
        sort($data);
        foreach ($data as $value) {
            static $i = 0;
            $resdata[] = ['id' => $i++, 'data' => $value];
        }
        return $this->dbactions->message(
            "data fetched successfully",
            $resdata
        );

    }
    //  get financial years dropdown api

    public function getFinancialYears()
    {
        try {
            $financialYears = DB::table('financial_years')
                ->select(DB::raw('CONCAT(years, "-", years + 1) AS financialYears'), DB::raw('MIN(id) AS id'))
                ->groupBy('years')
                ->orderBy('years', 'desc')
                ->get();


            return $this->dbactions->message('data fetched successfully', $financialYears);

        } catch (Exception $e) {
            return $this->dbactions->serverErrorMessage($e->getMessage());
        }


    }
    //  get financial years dropdown api

    public function getMonths()
    {
        try {
            $months = DB::table('months')
                ->select('id', 'month')
                ->orderBy('id')
                ->get();


            return $this->dbactions->message('data fetched successfully', $months);

        } catch (Exception $e) {
            return $this->dbactions->serverErrorMessage($e->getMessage());
        }


    }

}
