<?php

namespace App\Http\Controllers\PurchaseOrderScreen;

use Carbon\Carbon;
use App\Models\Milestone;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Methods\DbactionsController;

class MilestoneController extends Controller
{
    //
    private $dbactions;

    public function __construct()
    {
        $this->dbactions = new DbactionsController();
    }

    public function createMilestone(Request $request)
    {
        $response = "";

        $sanitizedInputs = $this->dbactions->getInputDatas([
            "poId",
            "milestoneDescription",
            "quantity"
        ]);

        if ($sanitizedInputs[0]) {
            return $sanitizedInputs[1];
        }

        [
            "poId" => $poId,
            "milestoneDescription" => $milestoneDescription,
            "quantity" => $quantity

        ] = $sanitizedInputs[1];

        if (!$this->dbactions->isExistsPoId($poId)) {
            $response = $this->dbactions->errorMessage("Please provide a valid purchase order number.");
        } else {

            $milestone = new Milestone();

            // $timestamp = Carbon::now("Asia/Kolkata");
            date_default_timezone_set("Asia/Kolkata");

            $milestone->po_id = (int) $poId;
            $milestone->milestone_description = $milestoneDescription;
            $milestone->quantity = (int) $quantity;

            $saved = $milestone->save();

            if ($saved) {
                $response = $this->dbactions->message('Milestone added successfully.');
            } else {
                $response = $this->dbactions->errorMessage('Failed to add milestone.');
            }

        }


        $this->dbactions->storeLog($request->path(), json_encode($sanitizedInputs[1]), $response->getContent());

        return $response;
    }

    //  get particular purchase order milestones
    public function getMilestones(Request $request)
    {
        $response = "";

        $sanitizedInputs = $this->dbactions->getInputDatas([
            "poId"
        ]);

        if ($sanitizedInputs[0]) {
            return $sanitizedInputs[1];
        }

        [
            "poId" => $poId

        ] = $sanitizedInputs[1];

        if (!$this->dbactions->isExistsPoId($poId)) {
            $response = $this->dbactions->errorMessage("Please provide a valid purchase order number.");
        } else {

            $milestones = Milestone::select("id AS milestoneId", "po_id AS purchaseOrderId", "milestone_description AS milestoneDescription", "quantity", "created_at AS createdAt")
                ->where("po_id", $poId)
                ->orderBy("createdAt", "asc")
                ->orderBy("milestoneId", "asc")
                ->get();

            $response = Response()->json(["message" => 'Milestone added successfully.', "data" => $milestones]);


        }


        $this->dbactions->storeLog($request->path(), json_encode($sanitizedInputs[1]), $response->getContent());

        return $response;
    }

    //  get particular purchase order milestones
    public function updateMilestone(Request $request)
    {
        $response = "";

        $sanitizedInputs = $this->dbactions->getInputDatas([
            "poId",
            "milestoneId",
            'milestoneDescription',
            'quantity'
        ]);

        if ($sanitizedInputs[0]) {
            return $sanitizedInputs[1];
        }

        [
            "poId" => $poId,
            "milestoneId" => $milestoneId,
            "milestoneDescription" => $milestoneDescription,
            "quantity" => $quantity

        ] = $sanitizedInputs[1];

        if (!$this->dbactions->isExistsPoId($poId)) {
            $response = $this->dbactions->errorMessage("Please provide a valid purchase order number.");
        } elseif (!$this->dbactions->isExistsMilestoneId($milestoneId)) {
            $response = $this->dbactions->errorMessage("Please provide a valid milestone id.");
        } else {

          
            $milestone = Milestone::where("id", $milestoneId)
                ->where("po_id", $poId)
                ->first();

            if ($milestone) {
                $milestone->milestone_description = $milestoneDescription;
                $milestone->quantity = $quantity;

                $milestone->save();

                $response = $this->dbactions->message('Milestone added successfully.');
            } else {

                $response = $this->dbactions->errorMessage('Faild to update milestone details.');

            }
        }

        $this->dbactions->storeLog($request->path(), json_encode($sanitizedInputs[1]), $response->getContent());

        return $response;
    }




}
