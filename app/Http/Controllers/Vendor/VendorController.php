<?php
namespace App\Http\Controllers\Vendor;


use Error;

use Exception;
use Illuminate\Http\Request;
use App\Models\Vendors as User;
use App\Models\Vendors as Users;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Methods\DbactionsController;

class VendorController extends Controller
{

    private $dbactions;


    // Constructor method
    public function __construct()
    {
        $this->dbactions = new DbactionsController();
    }


    public function loginPost(Request $request)
    {
        $response = "";
        try { // sanitize  the input datas

            $validationResult = $this->dbactions->getInputDatas(['email', 'password']);

            // throw new Exception("throwing server error message");

            if ($validationResult[0])
                return $validationResult[1];

            // sanitized input datas
            ["email" => $email, "password" => $password] = $validationResult[1];

            $email = filter_var($email, FILTER_SANITIZE_EMAIL);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response = $this->dbactions->errorMessage('Invalid email address');
            } else {
                // Check if user exists and retrieve the first result     
                $user = $this->dbactions->getUserWithEmail($email);

                if (!$user) {
                    $response = $this->dbactions->errorMessage('Please provide a valid email and password.');
                } else {

                    // Verify the password
                    if (Hash::check($password, $user->password)) {
                        $response = $this->dbactions->message('Logged in successfully.', [
                            'userId' => $user->id,
                            "roleName" => (DB::table('user_role')->where("id", $user->role_id)->get()->first())->role_name ?? "no Role",
                            'roleId' => $user->role_id,
                            'userName' => $user->username,
                            'themeColor' => $this->dbactions->getThemeNumber($user->id)
                        ]);
                    } else {
                        $response = $this->dbactions->errorMessage('Please provide a valid email and password.');
                    }
                }
            }

            $this->dbactions->storeLog($request->path(), json_encode($validationResult[1]), $response->getContent());

            return $response;
        } catch (Error | Exception $e) {
            return $this->dbactions->serverErrorMessage($e->getMessage());
        }
    }

    public function registerPost(Request $request)
    {
        $response = "";
        try {
            //    sanitize the input datas
            $validationResult = $this->dbactions->getInputDatas(['userName', 'roleId', 'email', 'password']);

            if ($validationResult[0])
                return $validationResult[1];
            //    sanitized input
            ['userName' => $userName, 'roleId' => $roleId, 'email' => $email, 'password' => $password] = $validationResult[1];

            $email = filter_var($email, FILTER_SANITIZE_EMAIL);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response = $this->dbactions->errorMessage('Invalid email address');
            } elseif ($this->dbactions->getUserWithEmail($email)) {
                $response = $this->dbactions->errorMessage('User email address already exists.');
            } else {

                $pass = Hash::make($password);

                $createdat = now(); // Example of getting the current timestamp

                $result = User::insert([
                    'username' => $userName,
                    'role_id' => $roleId,
                    'email' => $email,
                    'password' => $pass,
                    'created_at' => $createdat,
                    'updated_at' => $createdat
                ]);
                // Return a success message
                if ($result)
                    $response = $this->dbactions->message('User registered successfully.');
                else
                    $response = $this->dbactions->errorMessage('Faild to register user.');
            }

            $this->dbactions->storeLog($request->path(), json_encode($validationResult[1]), $response->getContent());
            return $response;

        } catch (Exception $e) {
            return $this->dbactions->serverErrorMessage($e->getMessage());
        }

    }





    // public function logout()
    // {
    //     Auth::guard('admin')->logout();
    //     return response()->json(['message' => 'Successfully logged out'], 200);
    // }
}
