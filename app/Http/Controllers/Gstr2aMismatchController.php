<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Methods\DbactionsController;

class Gstr2aMismatchController extends Controller
{
    private $dbactions;
    public function __construct()
    {
        $this->dbactions = new DbactionsController();
    }
    public function checkMismatch(Request $request)
    {
        $this->dbactions->message('GSTR2A');
      /*  // Validate the request data
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Query to fetch GSTR-2A data
        $gstr2aData = DB::table('gstr2a')
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->get();

        // Query to fetch purchase register data
        $purchaseData = DB::table('purchase_register')
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->get();

        // Logic to check mismatches
        $mismatches = [];
        foreach ($gstr2aData as $gstr2a) {
            $purchase = $purchaseData->firstWhere('invoice_number', $gstr2a->invoice_number);

            if (!$purchase || $purchase->tax_amount != $gstr2a->tax_amount) {
                $mismatches[] = [
                    'invoice_number' => $gstr2a->invoice_number,
                    'gstr2a_tax_amount' => $gstr2a->tax_amount,
                    'purchase_tax_amount' => $purchase ? $purchase->tax_amount : 'Not found',
                ];
            }
        }

        return view('gstr2a-mismatch-report', ['mismatches' => $mismatches]);
        */
    }
}
