<?php


namespace App\Http\Controllers\PurchaseOrderScreen;

use Carbon\Carbon;
use App\Models\Invoices;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Methods\DbactionsController;

class PurchaseOrderController extends Controller
{

    private $dbactions;
    //


    public function __construct()
    {

        $this->dbactions = new DbactionsController();
    }

    //  get purchase orders details
    public function getPurchaseOrdersList(Request $request)
    {
        $response = '';
        $loginUserId = $request->input("loginUserId") ?? "";

        $poNumber = $request->input("poNumber") ?? "";
        $gstNumber = $request->input("gstNumber") ?? "";
        $vendorName = $request->input("vendorName") ?? "";

        $requestDatas = ["loginUserId" => $loginUserId, "poNumber" => $poNumber, "gstNumber" => $gstNumber, "vendorName" => $vendorName];

        $query = PurchaseOrder::with('vendor'); // Eager load vendor
        if ($loginUserId == null) {
            $response = $this->dbactions->errorMessage("Please provide a login user id.");
        } else {

            if ($poNumber == null && $gstNumber == null && $vendorName == null) {
                $query->where('requester_id', $loginUserId);
                $query->orderBy('created_at', 'asc');
                $purchaseOrders = $query->get();
            } else {
                $query->where('requester_id', $loginUserId);
              
                if ($poNumber) {
               
                    $query->where('po_number', $poNumber);
                }

                if ($vendorName) {
                    $query->whereHas('vendor', function ($query) use ($vendorName) {
                        $query->where('username', $vendorName);
                    });
                }

                if ($gstNumber) {
                    $query->where('gst_no', $gstNumber);
                }

                $query->orderBy('created_at', 'asc');
                $purchaseOrders = $query->get();
            }

            if ($purchaseOrders->isEmpty()) {
                $response = $this->dbactions->message("No data matched", $purchaseOrders);
            }else{
                $datas = array();
                foreach ($purchaseOrders as $purchaseOrder) {
                    $datas[] = [
                        "poId" => $purchaseOrder->id,
                        "gstNumber" => $purchaseOrder->gst_no,
                        'vendorName' => $purchaseOrder->vendor->username ?? null, // Access vendor data
                        'vendorCode' => $purchaseOrder->vendor->vendor_code ?? null, // Access vendor data
                        'region' => $purchaseOrder->vendor->region ?? null,// Access vendor data
                        'createdAt' => $purchaseOrder->created_at->format('Y-m-d'),
                    ];
                }
                $response = $this->dbactions->message("data fetched successfully", $datas);
            }         
        }

        $this->dbactions->storeLog($request->path(), $requestDatas, $response->getContent());

        return $response;
    }



    public function postPurchaseOrder(Request $request)
    {
        $response = "";

        $fields = [
            'loginUserId',
            "vendorId",
            "poNumber",
            "lineItemDescription",
            "quantity",
            "rate",
            "amount",
            "hsn",
            "taxRate",
            "taxAmount"
        ];
        $validationResult = $this->dbactions->getInputDatas($fields);
        if ($validationResult[0]) {
            return $validationResult[1];
        }

        [
            "loginUserId" => $loginUserId,
            "vendorId" => $vendorId,
            "poNumber" => $poNumber,
            "lineItemDescription" => $lineItemDescription,
            "quantity" => $quantity,
            "rate" => $rate,
            "amount" => $amount,
            "hsn" => $hsn,
            "taxRate" => $taxRate,
            "taxAmount" => $taxAmount
        ] = $validationResult[1];



        if ($this->dbactions->isExistsPONumber($poNumber)) {
            $response = $this->dbactions->errorMessage('The given po number is exists.');
        } elseif (!$this->dbactions->isRequester($loginUserId)) {
            $response = $this->dbactions->errorMessage('Only requesters and admins have permission to create purchase orders.');
        } else {

            //  get and save the image data

            $imagePath = "";

            // Handle the file upload
            if ($request->hasFile("imageData")) {

                $validationErr = Validator::make($request->all(), [
                    "imageData" => 'image|mimes:jpeg,png,jpg|max:3000'
                ]);


                if ($validationErr->fails()) {
                    $errrorMsg = $validationErr->errors()->first();
                    $response = $this->errorMessage($errrorMsg);
                    $this->dbactions->storeLog($request->path(), json_encode($validationResult[1]), $response->getContent());
                    return $response;
                }

                $file = $request->file("imageData");
                $extension = $file->getClientOriginalExtension();

                $tempfilename = 'Img_po_order' . round(rand() * 100) . '.' . $extension;
                // creating a storage path

                $file->move(public_path('images\purchase_orders'), $tempfilename);

                $imagePath = $tempfilename;

            }


            $purchaseOrder = new PurchaseOrder();

            $timestamp = Carbon::now('Asia/Kolkata');

            $purchaseOrder->requester_id = (int) $loginUserId;
            $purchaseOrder->vendor_id = (int) $vendorId;
            $purchaseOrder->po_number = $poNumber;
            $purchaseOrder->line_item_description = $lineItemDescription;
            $purchaseOrder->quantity = (int) $quantity;
            $purchaseOrder->rate = (double) $rate;
            $purchaseOrder->amount = (double) $amount;
            $purchaseOrder->hsn = $hsn;
            $purchaseOrder->tax_rate = (float) $taxRate;
            $purchaseOrder->tax_amount = (double) $taxAmount;
            $purchaseOrder->file_path = $imagePath;

            $purchaseOrder->created_at = $timestamp;
            $purchaseOrder->updated_at = $timestamp;

            $result = $purchaseOrder->save();

            if ($result) {
                // Success: the invoice was updated
                $response = $this->dbactions->message('Purchase order created successfully.');
            } else {
                // Failure: the invoice could not be updated
                $response = $this->dbactions->errorMessage('Failed to create purchase order.');
            }

        }
        $this->dbactions->storeLog($request->path(), json_encode($validationResult[1]), $response->getContent());

        return $response;
    }



}
