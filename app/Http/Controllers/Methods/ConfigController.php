<?php

namespace App\Http\Controllers\Methods;



use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class ConfigController
    // class DbactionsController extends Controller
{

    // sanitise the input data
    public function test_input($data)
    {
        $data = strip_tags($data);
        $data = htmlspecialchars($data);
        $data = stripslashes($data);
        $data = trim($data);
        return $data;
    }
    // Return a response message
    public function message($message, $data = null)
    {
        if ($data != null) {
            return Response()->json([
                'message' => $message,
                'error' => false,
                'data' => $data
            ], 200);
        } else {
            return Response()->json([
                'message' => $message,
                'error' => false
            ], 200);
        }
    }
    // Return a Server Error Message
    public function errorMessage($message)
    {
        return Response()->json([
            'message' => $message,
            'error' => true
        ], 200);
    }
    public function serverErrorMessage($message)
    {
        $response = Response()->json([
            'message' => $message,
            'error' => true
        ], 500);

        // $this->storeLog(Request()->path(), json_encode(Request()->all()), $response->getContent());
        $this->storeLog(Request()->path(), '', $response->getContent());
        return $response;
    }

    // response []

    public function responseArray($message, $data = null)
    {
        if ($data != null) {
            return Response()->json([
                'message' => $message,
                'error' => false,
                'data' => $data
            ], 200);
        } else {
            return Response()->json([
                'message' => $message,
                'error' => false,
                'data' => []
            ], 200);
        }
    }


    // validate the input field are present or not (re-Usable code)
    public function getInputDatas($fields)
    {
        $response = "";

        $request = Request();

        $inputFields = [];
        foreach ($fields as $inputKey) {
            $inputFields[$inputKey] = $this->test_input($request->input($inputKey) ?? "");
        }

        foreach ($inputFields as $key => $value) {

            if ($value == null) {
                $key = strtolower(preg_replace('/(?<!^)([A-Z])/', ' $1', $key));
                $response = $this->errorMessage('Please provide a value of ' . $key . '.');
                $this->storeLog($request->path(), json_encode($inputFields), $response->getContent());
                return [true, $response];
            }
        }
        return [false, $inputFields];
    }

    public function saveImageWithValidation($request, $fileName)
    {
        /* Save Images to public folders (only use if image is mandatory)
         *  the image was successfully saved then return a saved image new name.
         *  the image was not saved then throw corresponding error message to client.
         */

        // Validate the request
        $validator = Validator::make($request->all(), [
            $fileName => 'required|image|mimes:jpeg,png,jpg|max:5000',
        ]);
        // Return The Errors one by one
        if ($validator->fails()) {
            $errrorMsg = $validator->errors()->first();
            return [true, $this->errorMessage($errrorMsg)];

        }

        // Handle the file upload
        if ($request->hasFile($fileName)) {
            $file = $request->file($fileName);
            $extension = $file->getClientOriginalExtension();

            $tempfilename = 'Img_invoice_' . round(rand() * 100) . '.' . $extension;
            // creating a storage path

            $file->move(public_path('images\invoices'), $tempfilename);

            /* 
            $tempfilename = 'Img' . now()->format('Y-m-d_H-i-s') . '.' . $extension;
            // creating a storage path
            $path = $file->storeAs('images', $tempfilename, 'public');
            // save image to Storage/app/public/images
            // $url = url('/') . Storage::url($path);
            */

            return [false, $tempfilename];
        } else {
            return [true, $this->errorMessage('Image upaload failed')];

        }
    }

    // Get given file name image path

    public function getPurchaseOrdersURL($fileName)
    {
        $fullURL = url('/') . '/images/purchase_orders/' . $fileName;
        return $fullURL;

    }
    public function getInvoicesURL($fileName)
    {
        $fullURL = url('/') . '/images/invoices/' . $fileName;
        return $fullURL;

    }


    public function storeLog($api, $request, $response)
    {
        date_default_timezone_set('Asia/Kolkata');
        $createdAt = date("Y-m-d H:i:s", time());

        $createdTime = 'TRIGGERED ON: ' . $createdAt;
        $api = 'LINK: ' . 'http://localhost:8000/' . $api;
        // $request = 'REQUEST: ' . is_array($request) ? serialize($request) : (string) $request;
        $request = 'REQUEST: ' . is_array($request) ? json_encode($request) : (string) $request;
        $response = 'RESPONSE: ' . $response;

        // Read the existing content of the log file
        $existingContent = file_get_contents('log.txt');

        // Combine the new log entry with the existing content
        $newEntry = $createdTime . PHP_EOL . $api . PHP_EOL . $request . PHP_EOL . $response . PHP_EOL . PHP_EOL;
        $updatedContent = $newEntry . $existingContent;

        // Write the updated content back to the log file
        $myfile = file_put_contents('log.txt', $updatedContent);

        // $myfile = file_put_contents('log.txt', $createdTime.PHP_EOL.$api.PHP_EOL.$request.PHP_EOL.$response.PHP_EOL.PHP_EOL, FILE_APPEND | LOCK_EX);
        return $myfile;
    }

}
