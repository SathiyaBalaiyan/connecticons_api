<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Methods\DbactionsController;

class AdminController extends Controller
{
    //

    private $dbactions;

    public function __construct()
    {
        $this->dbactions = new DbactionsController();
    }

    public function addUserRole(Request $request)
    {
        $response = '';

        $sanitizedInputs = $this->dbactions->getInputDatas(["loginUserId", "roleName"]);
        $roleId = $this->dbactions->test_input($request->input("roleId") ?? "");
        if ($sanitizedInputs[0]) {
            return $sanitizedInputs[1];
        }

        ["roleName" => $roleName, "loginUserId" => $loginUserId] = $sanitizedInputs[1];
        if (!$this->dbactions->isAdmin($loginUserId)) {
            $response = $this->dbactions->errorMessage("Admnin only have acces to insert roles.");
        } else {

            if ((int) $roleId != null) {

                $affected = DB::update('UPDATE  user_role SET role_name = :roleName where id = :id', [
                    'roleName' => $roleName,
                    'id' => $roleId
                ]);

                if ($affected) {
                    $response = $this->dbactions->errorMessage("Role updated successfully.");
                } else {
                    $response = $this->dbactions->errorMessage("Faild to update role name.");
                }


            } else {

                $result = DB::table("user_role")->select('*')->get();

                $isDuplicate = false;

                foreach ($result as $role) {

                    if (strtolower(trim($roleName)) == strtolower(trim($role->role_name))) {
                        $isDuplicate = true;
                        break;
                    }
                }
                $currentTimestamp = Carbon::now('Asia/Kolkata');
                if (!$isDuplicate) {
                    $result = DB::table("user_role")->insert([
                        "role_name" => $roleName,
                        "created_at" => $currentTimestamp
                    ]);

                    if ($result) {
                        $response = $this->dbactions->message("User role inserted successfully.");
                    } else {
                        $response = $this->dbactions->errorMessage("Faild to insert user role.");
                    }

                } else {
                    $response = $this->dbactions->errorMessage("User role already exists.");
                }

            }
        }

        $this->dbactions->storeLog($request->path(), json_encode($sanitizedInputs[1]), $response->getContent());

        return $response;
    }

}
