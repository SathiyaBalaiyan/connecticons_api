<?php

namespace App\Http\Controllers\Invoice;


use Response;
use Exception;
use Carbon\Carbon;
// use Carbon\Traits\Date;
use App\Models\Invoices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Methods\DbactionsController;

class InvoiceController extends Controller
{
    // Private property
    private $dbactions;

    // Constructor method
    public function __construct()
    {
        $this->dbactions = new DbactionsController();
    }


    //  Insert Invoice Details
    public function InsertInvoice(Request $request)
    {
        $response = "";
        try {
            $validator = Validator::make($request->all(), [
                'LUTExpireDate' => 'required|date',
                'invoiceDate' => 'required|date',
            ], [
                'LUTExpireDate.required' => 'Please provide a LUT expire date.',
                'LUTExpireDate.date' => 'LUT expire date must be a date.',
                'invoiceDate.required' => 'Please provide a invoice date.',
                'invoiceDate.date' => 'invoice date must be a date',
            ]);
            if ($validator->fails()) {
                $errorsdatas = $validator->errors()->toArray();
                // Validation fails, handle the errors here
                foreach ($errorsdatas as $key => $messages) {
                    foreach ($messages as $message) {
                        return $this->dbactions->errorMessage($message);
                    }
                }
            }
            // Date validation (pending...) 

            $invoiceDate = $this->dbactions->test_input($request->input("invoiceDate") ?? "");
            $LUTExpireDate = $this->dbactions->test_input($request->input("LUTExpireDate") ?? "");

            $validationResult = $this->dbactions->getInputDatas(
                [
                    'vendorId',
                    'vendorInvoiceNo',
                    'currency',
                    'grossAmount',
                    'taxAmount',
                    'taxableValue',
                    'businessPlace',
                    'taxRate',
                    'vendorCode',
                    'vendorGST',
                    'GSTNo',
                    'POS',
                    'taxType',
                    'requesterName',
                    'LUTNo',
                    'ewayBillNo',
                    'reasonDelayedSubmission',
                    'TCS',
                    'vendorType',
                    'vendorRemarks',
                    'clientRemarks',
                ]
            );
            if ($validationResult[0]) {
                return $validationResult[1];
            }
            [
                'vendorId' => $vendorId,
                'vendorInvoiceNo' => $vendorInvoiceNo,
                'currency' => $currencyType,
                'grossAmount' => $grossAmount,
                'taxAmount' => $taxAmount,
                'taxableValue' => $taxableValue,
                'businessPlace' => $businessPlace,
                'taxRate' => $taxRate,
                'vendorCode' => $vendorCode,
                'vendorGST' => $vendorGST,
                'GSTNo' => $GSTNo,
                'POS' => $POS,
                'taxType' => $taxType,
                'requesterName' => $requesterName,
                'LUTNo' => $LUTNo,
                'ewayBillNo' => $ewayBillNo,
                'reasonDelayedSubmission' => $reasonDelayedSubmission,
                'TCS' => $TCS,
                'vendorType' => $vendorType,
                'vendorRemarks' => $vendorRemarks,
                'clientRemarks' => $clientRemarks
            ] = $validationResult[1];


            $hasValidVendor = $this->dbactions->vendorExists($vendorId);

            if (!$hasValidVendor) {
                $response = $this->dbactions->errorMessage('Please provide a valid vendor id');
            } elseif ($this->dbactions->invoiceExists($vendorInvoiceNo)) {
                $response = $this->dbactions->errorMessage("vendor Invoice Number already exists");
            } else {


                // ----------------------- Get image input...
                $img_result = $this->dbactions->saveImageWithValidation($request, "invoiceImage");

                if ($img_result[0]) {
                    $response = $img_result[1];
                }
                $imageName = $img_result[1];
                $invoice = new Invoices();


                $invoice->vendor_id = $vendorId;
                $invoice->vendor_invoice_no = $vendorInvoiceNo;
                $invoice->currency = strtoupper($currencyType);
                $invoice->gross_amount = $grossAmount;
                $invoice->tax_amount = $taxAmount;
                $invoice->taxable_value = $taxableValue;

                $invoice->business_place = $businessPlace;
                $invoice->tax_rate = $taxRate;
                $invoice->invoice_date = Carbon::parse($invoiceDate);
                $invoice->vendor_code = $vendorCode;
                $invoice->vendor_gst_no = $vendorGST;

                $invoice->gst_no = $GSTNo;
                $invoice->pos = $POS;
                $invoice->tax_type = $taxType;
                $invoice->requester_name = $requesterName;
                $invoice->lut_number = $LUTNo;

                $invoice->lut_expire_date = Carbon::parse($LUTExpireDate);
                $invoice->eway_bill_no = $ewayBillNo;
                $invoice->reason_delayed_submission = $reasonDelayedSubmission;
                $invoice->tcs = $TCS;
                $invoice->vendor_type = $vendorType;
                $invoice->vendor_remarks = $vendorRemarks;
                $invoice->client_remarks = $clientRemarks;
                $invoice->invoice_image = $imageName;

                // Set the current timestamp for created_at and updated_at
                $currentTimestamp = Carbon::now('Asia/Kolkata');
                $invoice->created_at = $currentTimestamp;
                $invoice->updated_at = $currentTimestamp;
                // Save the invoice to the database
                $saved = $invoice->save();

                if ($saved) {
                    $response = $this->dbactions->message('Invoice added successfully');
                } else {
                    $response = $this->dbactions->errorMessage('Failed to add invoice');
                }
            }

            $this->dbactions->storeLog($request->path(), json_encode($validationResult[1]), $response->getContent());

            return $response;

        } catch (Exception $e) {
            return $this->dbactions->serverErrorMessage($e->getMessage());
            // return $this->dbactions->serverErrorMessage('Internal Server Error');
        }

    }

    // Update Invoice Details Using Vendor Invoice Id

    public function UpdateInvoice(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'LUTExpireDate' => 'required|date',
                'invoiceDate' => 'required|date'
            ], [
                'LUTExpireDate.required' => 'LUT expire date should not be empty',
                'LUTExpireDate.date' => 'LUT expire date must be a date.',
                'invoiceDate.required' => 'invoice date should not be empty',
                'invoiceDate.date' => 'invoice date should not be empty',
            ]);
            if ($validator->fails()) {
                $errorsdatas = $validator->errors()->toArray();
                // Validation fails, handle the errors here
                foreach ($errorsdatas as $key => $messages) {
                    foreach ($messages as $message) {
                        return $this->dbactions->errorMessage($message);
                    }
                }
            }
            // Date validation (pending...) 
            $invoiceDate = $this->dbactions->test_input($request->input("invoiceDate") ?? "");
            $LUTExpireDate = $this->dbactions->test_input($request->input("LUTExpireDate") ?? "");

            $validationResult = $this->dbactions->getInputDatas(

                [
                    'vendorId',
                    'vendorInvoiceNo',
                    'currency',
                    'grossAmount',
                    'taxAmount',
                    'taxableValue',
                    'businessPlace',
                    'taxRate',
                    'vendorCode',
                    'vendorGST',
                    'GSTNo',
                    'POS',
                    'taxType',
                    'requesterName',
                    'LUTNo',
                    'ewayBillNo',
                    'reasonDelayedSubmission',
                    'TCS',
                    'vendorType',
                    'vendorRemarks',
                    'clientRemarks',
                ]
            );
            if ($validationResult[0]) {
                return $validationResult[1];
            }
            [
                'vendorId' => $vendorId,
                'vendorInvoiceNo' => $vendorInvoiceNo,
                'currency' => $currencyType,
                'grossAmount' => $grossAmount,
                'taxAmount' => $taxAmount,
                'taxableValue' => $taxableValue,
                'businessPlace' => $businessPlace,
                'taxRate' => $taxRate,
                'vendorCode' => $vendorCode,
                'vendorGST' => $vendorGST,
                'GSTNo' => $GSTNo,
                'POS' => $POS,
                'taxType' => $taxType,
                'requesterName' => $requesterName,
                'LUTNo' => $LUTNo,
                'ewayBillNo' => $ewayBillNo,
                'reasonDelayedSubmission' => $reasonDelayedSubmission,
                'TCS' => $TCS,
                'vendorType' => $vendorType,
                'vendorRemarks' => $vendorRemarks,
                'clientRemarks' => $clientRemarks
            ] = $validationResult[1];


            // Find the invoice by vendorInvoiceNo  
            $invoice = Invoices::where('vendor_invoice_no', $vendorInvoiceNo)
                ->where('vendor_id', $vendorId)
                ->first();

            if ($invoice) {
                //  Get and Validate image input...
                if ($request->hasFile("invoiceImage")) {
                    $validator = Validator::make($request->all(), [
                        "invoiceImage" => 'required|image|mimes:jpeg,png,jpg,gif|max:5000',
                    ]);
                    if ($validator->fails()) {
                        // Validation fails, handle the errors here
                        $errors = $validator->errors()->all();
                        return $this->dbactions->errorMessage($errors[0]);
                    }

                    $img_result = $this->dbactions->saveImageWithValidation($request, "invoiceImage");
                    if ($img_result[0]) {
                        return $img_result[1];
                    }
                    $imageName = $img_result[1];
                }
                // Update the fields
                $invoice->vendor_invoice_no = $vendorInvoiceNo;
                $invoice->currency = strtoupper($currencyType);
                $invoice->gross_amount = $grossAmount;
                $invoice->tax_amount = $taxAmount;
                $invoice->taxable_value = $taxableValue;

                $invoice->business_place = $businessPlace;
                $invoice->tax_rate = $taxRate;
                $invoice->invoice_date = Carbon::parse($invoiceDate);
                $invoice->vendor_code = $vendorCode;
                $invoice->vendor_gst_no = $vendorGST;

                $invoice->gst_no = $GSTNo;
                $invoice->pos = $POS;
                $invoice->tax_type = $taxType;
                $invoice->requester_name = $requesterName;
                $invoice->lut_number = $LUTNo;

                $invoice->lut_expire_date = Carbon::parse($LUTExpireDate);
                $invoice->eway_bill_no = $ewayBillNo;
                $invoice->reason_delayed_submission = $reasonDelayedSubmission;
                $invoice->tcs = $TCS;
                $invoice->vendor_type = $vendorType;
                $invoice->vendor_remarks = $vendorRemarks;
                $invoice->client_remarks = $clientRemarks;
                if ($request->hasFile("invoiceImage")) {
                    $invoice->invoice_image = $imageName;
                }
                // Set the updated_at timestamp
                $invoice->updated_at = Carbon::now('Asia/Kolkata');

                // Save the updated invoice
                $result = $invoice->save();

                if ($result) {
                    // Success: the invoice was updated
                    return $this->dbactions->message('Invoice updated successfully');
                } else {
                    // Failure: the invoice could not be updated
                    return $this->dbactions->errorMessage('Failed to update invoice');
                }
            } else {
                // invoice number and vendor id mismatch
                return $this->dbactions->errorMessage("Invalid Invoice Number or Vendor Id");
            }

        } catch (Exception $e) {
            return $this->dbactions->serverErrorMessage($e->getMessage());
        }

    }

    //  Get Invoice Using vendoirInvoice Id  Using VendorInvoiceNo

    public function GetInvoice(Request $request)
    {
        // Retrieve the invoice ID from the request
        $validationResult = $this->dbactions->getInputDatas([
            'vendorId',
            'vendorInvoiceNo'
        ]);
        if ($validationResult[0]) {
            return $validationResult[1];
        }
        ['vendorId' => $vendorId, 'vendorInvoiceNo' => $vendorInvoiceNo] = $validationResult[1];

        $invoice = Invoices::where('vendor_invoice_no', $vendorInvoiceNo)->where('vendor_id', $vendorId)
            ->first();
        // Return only the specified fields
        if (!$invoice) {
            return $this->dbactions->errorMessage("no invoice data matched");
        }
        $data = [
            'vendorInvoiceNo' => $invoice->vendor_invoice_no,
            'currency' => $invoice->currency,
            'grossAmount' => $invoice->gross_amount,
            'taxAmount' => $invoice->tax_amount,
            'taxableValue' => $invoice->taxable_value,
            'businessPlace' => $invoice->business_place,
            'taxRate' => $invoice->tax_rate,
            'invoiceDate' => $invoice->invoice_date,
            'vendorCode' => $invoice->vendor_code,
            'vendorGST' => $invoice->vendor_gst_no,
            'GSTNo' => $invoice->gst_no,
            'POS' => $invoice->pos,
            'taxType' => $invoice->tax_type,
            'requesterName' => $invoice->requester_name, // Example with Alice as the name
            'LUTNo' => $invoice->lut_number,
            'LUTExpireDate' => $invoice->lut_expire_date,
            'ewayBillNo' => $invoice->eway_bill_no,
            'reasonDelayedSubmission' => $invoice->reason_delayed_submission,
            'TCS' => $invoice->tcs,
            'vendorType' => $invoice->vendor_type,
            'vendorRemarks' => $invoice->vendor_remarks,
            'clientRemarks' => $invoice->client_remarks,
            'invoiceImage' => $invoice->invoice_image,
        ];

        return $this->dbactions->message("data fetched successfully", $data);

    }

    public function getInvoicesList(Request $request)
    {

        // $userId = $this->dbactions->test_input($request->input("userId") ?? "");
        $validationResult = $this->dbactions->getInputDatas([
            'userId'
        ]);
        if ($validationResult[0]) {
            return $validationResult[1];
        }
        [
            'userId' => $userId
        ] = $validationResult[1];
        // Retrieve the invoice ID from the request
        // $invoice = Invoices::findOrFail("vendor_invoice_no", $vendorInvoiceNo); // Retrieve the invoice by ID

        $invoice_datas = Invoices::where("vendor_id", $userId)->get();
        // Return only the specified fields
        if ($invoice_datas->isEmpty()) {
            // success message because request full field...
            return $this->dbactions->message("no data matched", $invoice_datas);
        }
        $datas = array();
        foreach ($invoice_datas as $invoice) {
            $datas[] = [
                'atachedInvoice' => "Hyperlink to invoice",
                'invoiceNumber' => $invoice->vendor_invoice_no,
                'invoiceDate' => $invoice->invoice_date,
                'submissionDate' => $invoice->created_at,
                'grossAmount' => $invoice->gross_amount,

                'PONumber' => $invoice->pos,
                'requesterName' => $invoice->requester_name,
                'receiptStatus' => "received",
                'GSTR2A' => "matched",
                'paymentStatus' => "success",
            ];
        }
        return $this->dbactions->message("data fetched successfully", $datas);

    }


    //  Filter invoice datas 

    public function invoiceSearch(Request $request)
    {

        $loginUserId = $this->dbactions->test_input($request->input("loginUserId") ?? "");
        $financialYear = $this->dbactions->test_input($request->input("financialYear") ?? "");
        $gstNumber = $this->dbactions->test_input($request->input("gstNumber") ?? "");
        $startDate = $this->dbactions->test_input($request->input("startDate") ?? "");
        $endDate = $this->dbactions->test_input($request->input("endDate") ?? "");




        $validator = Validator::make($request->all(), [
            'loginUserId' => 'required|integer',
            'financialYear' => 'integer',
            'startDate' => 'date',
            'endDate' => 'date',
        ], [
            'loginUserId.required' => 'Please provide a value of login user id.',
            'financialYear.date' => 'Financial year must be a date.',
            'startDate.date' => 'Invoice start date must be a date.',
            'endDate.date' => 'Invoice end date must be a date',
        ]);

        if ($validator->fails()) {
            $errorsdatas = $validator->errors()->toArray();
            // Validation fails, handle the errors here
            foreach ($errorsdatas as $key => $messages) {
                foreach ($messages as $message) {
                    return $this->dbactions->errorMessage($message);
                }
            }
        }

        // For financial year 2023-2024

        // $gstPattern = '^(\\d{2}[A-Z]{5}\\d{4}[A-Z]{1}[A-Z\\d]{1}[Z]{1}[A-Z\\d]{1})$';


        $query = Invoices::query();

        $query->where('vendor_id', $loginUserId);
        // Financial year range
        if ($financialYear) {

            $startDate_year = Carbon::create($financialYear, 4, 1)->startOfDay(); // April 1st of the given year
            $endDate_year = Carbon::create($financialYear + 1, 3, 31)->endOfDay(); // March 31st of the following year
       
            $query->whereBetween('invoice_date', [$startDate_year, $endDate_year]);
        }

        // Specific date range
        if ($startDate) {
            $query->where('invoice_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('invoice_date', '<=', $endDate);
        }

        // GST number filter
        if ($gstNumber) {
            $query->where('gst_no', 'like', '%' . $gstNumber . '%');
        }
        // Order by invoice date

        $query->orderBy('invoice_date', 'asc');
        $invoice_datas = $query->get();

        // Return only the specified fields
        if ($invoice_datas->isEmpty()) {
            // success message because request full field...
            return $this->dbactions->message("no data matched", $invoice_datas);
        }
        $datas = array();
        foreach ($invoice_datas as $invoice) {
            $datas[] = [
                "GST NUMber" => $invoice->gst_no,
                'atachedInvoice' => "Hyperlink to invoice",
                'invoiceNumber' => $invoice->vendor_invoice_no,
                'invoiceDate' => $invoice->invoice_date,
                'submissionDate' => $invoice->created_at->format('Y-m-d'),
                'grossAmount' => $invoice->gross_amount,

                'PONumber' => $invoice->pos,
                'requesterName' => $invoice->requester_name,
                'receiptStatus' => "received",
                'GSTR2A' => "matched",
                'paymentStatus' => "success",
            ];
        }
        return $this->dbactions->message("data fetched successfully", $datas);

    }

    //  Demo chart code
    public function DEMOCHARTCODE()
    {
        return $this->dbactions->message(
            "Demo Response",
            [
                ['id' => 0, 'value' => 10, 'label' => 'series A'],
                ['id' => 1, 'value' => 15, 'label' => 'series B'],
                ['id' => 2, 'value' => 20, 'label' => 'series C'],
                ['id' => 3, 'value' => 35, 'label' => 'series D'],
                ['id' => 4, 'value' => 5, 'label' => 'series E'],
                ['id' => 5, 'value' => 15, 'label' => 'series F'],
            ]
        );
    }




}