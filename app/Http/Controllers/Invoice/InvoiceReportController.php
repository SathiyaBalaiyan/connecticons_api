<?php

namespace App\Http\Controllers\Invoice;

use Exception;
use App\Models\Invoices;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Methods\DbactionsController;

class InvoiceReportController extends Controller
{
    //
    private $dbactions;
    public function __construct()
    {
        $this->dbactions = new DbactionsController();
        // $this->dbactions = config("");
    }

    //  get report financial year wise 


    public function reportFinancialyearWise(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'financialYear' => 'required|integer|max:2065',
            'GSTNo' => 'required|string|max:15',
            'vendorId' => 'required|string|max:15',
            // Define other validation rules for additional fields if needed
        ], [
            'financialYear.required' => 'The financial year should not be empty',
            'financialYear.max' => 'The financial year must not exceed :max year.',
            'GSTNo.max' => 'The GST number must not exceed :max characters.',

        ]);
        if ($validator->fails()) {
            // return redirect()->back()->withErrors($validator)->withInput();
            $errorsdatas = $validator->errors()->toArray();
            foreach ($errorsdatas as $key => $messages) {
                foreach ($messages as $message) {
                    return $this->dbactions->errorMessage($message);
                }
            }
        }
        $vendorId = $this->dbactions->test_input($request->input("vendorId") ?? '');
        $financialYear = $this->dbactions->test_input($request->input("financialYear") ?? '');
        $GSTNo = $this->dbactions->test_input($request->input("GSTNo") ?? '');

        // Define the financial year range
        $startYear = $financialYear; // For example financial year 2023
        $endYear = $financialYear + 1; // For example financial year 2023-2024

        // Calculate the start and end dates of the financial year
        $startDate = $startYear . '-04-01'; // Financial year starts on April 1st
        $endDate = $endYear . '-03-31'; // Financial year ends on March 31st

        $invoice_datas = Invoices::where('gst_no', intval($GSTNo))
            ->where('vendor_id', $vendorId)
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->select('*') // Selected all columns
            ->orderBy('invoice_date', 'desc') // Order by invoice_date in descending order
            ->get();

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
    //  ------------------ get Rejected Invoice Reports

    public function reportRejectedInvoices(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'financialYear' => 'required|integer|max:2065',
            'month' => 'required|string|max:12',
            'GSTNo' => 'required|string|max:15',
            'vendorId' => 'required|string|max:15',
            // Define other validation rules for additional fields if needed
        ], [
            'financialYear.required' => 'The financial year should not be empty',
            'financialYear.max' => 'The financial year must not exceed :max year.',
            'month.max' => 'The month must not exceed :max year.',
            'GSTNo.max' => 'The GST number must not exceed :max characters.',

        ]);
        if ($validator->fails()) {
            // return redirect()->back()->withErrors($validator)->withInput();
            $errorsdatas = $validator->errors()->toArray();
            foreach ($errorsdatas as $key => $messages) {
                foreach ($messages as $message) {
                    return $this->dbactions->errorMessage($message);
                }
            }
        }
        $vendorId = $this->dbactions->test_input($request->input("vendorId") ?? '');
        $month = $this->dbactions->test_input($request->input("month") ?? '');
        $financialYear = $this->dbactions->test_input($request->input("financialYear") ?? '');
        $GSTNo = $this->dbactions->test_input($request->input("GSTNo") ?? '');

        // Define the financial year range
        // For example financial year 2023
        // $endYear = $financialYear; // For example financial year 2023-2024

        // Calculate the start and end dates of the financial year
        $startDate = $financialYear . '-' . $month . '-01'; // Financial year starts on April 1st
        $endDate = $financialYear . '-' . $month . '-31'; // Financial year ends on March 31sts

        $invoice_datas = Invoices::where('gst_no', intval($GSTNo))
            ->where('vendor_id', $vendorId)
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->select('*') // Selected all columns
            ->orderBy('invoice_date', 'desc') // Order by invoice_date in descending order
            ->get();

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
    //  ------------------ get GSTR2A mismatch reports

    public function reportGstr2aMismatch(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'financialYear' => 'required|integer|max:2065',
            'month' => 'required|string|max:12',
            'GSTNo' => 'required|string|max:15',
            'vendorId' => 'required|string|max:15',
            // Define other validation rules for additional fields if needed
        ], [
            'financialYear.required' => 'The financial year should not be empty',
            'financialYear.max' => 'The financial year must not exceed :max year.',
            'month.max' => 'The month must not exceed :max year.',
            'GSTNo.max' => 'The GST number must not exceed :max characters.',

        ]);
        if ($validator->fails()) {
            // return redirect()->back()->withErrors($validator)->withInput();
            $errorsdatas = $validator->errors()->toArray();
            foreach ($errorsdatas as $key => $messages) {
                foreach ($messages as $message) {
                    return $this->dbactions->errorMessage($message);
                }
            }
        }
        $vendorId = $this->dbactions->test_input($request->input("vendorId") ?? '');
        $month = $this->dbactions->test_input($request->input("month") ?? '');
        $financialYear = $this->dbactions->test_input($request->input("financialYear") ?? '');
        $GSTNo = $this->dbactions->test_input($request->input("GSTNo") ?? '');

        // Define the financial year range
        // For example financial year 2023
        // $endYear = $financialYear; // For example financial year 2023-2024

        // Calculate the start and end dates of the financial year
        $startDate = $financialYear . '-' . $month . '-01'; // Financial year starts on April 1st
        $endDate = $financialYear . '-' . $month . '-31'; // Financial year ends on March 31sts

        $invoice_datas = Invoices::where('gst_no', intval($GSTNo))
            ->where('vendor_id', $vendorId)
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->select('*') // Selected all columns
            ->orderBy('invoice_date', 'desc') // Order by invoice_date in descending order
            ->get();

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
    //  ------------------ get Open PO reports

    public function reportOpenPO(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'financialYear' => 'required|integer|max:2065',
            'month' => 'required|string|max:12',
            'GSTNo' => 'required|string|max:15',
            'vendorId' => 'required|string|max:15',
            // Define other validation rules for additional fields if needed
        ], [
            'financialYear.required' => 'The financial year should not be empty',
            'financialYear.max' => 'The financial year must not exceed :max year.',
            'month.max' => 'The month must not exceed :max year.',
            'GSTNo.max' => 'The GST number must not exceed :max characters.',

        ]);
        if ($validator->fails()) {
            // return redirect()->back()->withErrors($validator)->withInput();
            $errorsdatas = $validator->errors()->toArray();
            foreach ($errorsdatas as $key => $messages) {
                foreach ($messages as $message) {
                    return $this->dbactions->errorMessage($message);
                }
            }
        }
        $vendorId = $this->dbactions->test_input($request->input("vendorId") ?? '');
        $month = $this->dbactions->test_input($request->input("month") ?? '');
        $financialYear = $this->dbactions->test_input($request->input("financialYear") ?? '');
        $GSTNo = $this->dbactions->test_input($request->input("GSTNo") ?? '');

        // Define the financial year range
        // For example financial year 2023
        // $endYear = $financialYear; // For example financial year 2023-2024

        // Calculate the start and end dates of the financial year
        $startDate = $financialYear . '-' . $month . '-01'; // Financial year starts on April 1st
        $endDate = $financialYear . '-' . $month . '-31'; // Financial year ends on March 31sts

        $invoice_datas = Invoices::where('gst_no', intval($GSTNo))
            ->where('vendor_id', $vendorId)
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->select('*') // Selected all columns
            ->orderBy('invoice_date', 'desc') // Order by invoice_date in descending order
            ->get();

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


}
