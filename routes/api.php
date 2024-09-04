<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Dropdown\WebdataController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Invoice\InvoiceController;
use App\Http\Controllers\Vendor\VendorController;
use App\Http\Controllers\Dropdown\DropdownController;
use App\Http\Controllers\Gstr2aMismatchController;
use App\Http\Controllers\Invoice\InvoiceReportController;
use App\Http\Controllers\PurchaseOrderScreen\MilestoneController;
use App\Http\Controllers\PurchaseOrderScreen\PurchaseOrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

date_default_timezone_set("Asia/Kolkata");

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/login', [VendorController::class, 'loginPost']);
Route::post('/register', [VendorController::class, 'registerPost']);
// ================================>     Admin Actions  <========================
Route::post("/add_role", [AdminController::class, 'addUserRole']);
// --------------------------------  Invoice Routes
Route::post('/insert_invoice', [InvoiceController::class, 'InsertInvoice']);
Route::post('/update_invoice', [InvoiceController::class, 'UpdateInvoice']);
Route::post('/get_invoice', [InvoiceController::class, 'GetInvoice']);
Route::post('/invoices_list', [InvoiceController::class, 'getInvoicesList']);

Route::get('/get_chartData', [InvoiceController::class, 'DEMOCHARTCODE']);

// -------------------------------- drop-down api's 

Route::get('/get_roles', [DropdownController::class, 'getRoles']);



Route::get('/get_currencies', [DropdownController::class, 'getCurrencyDatas']);
Route::get('/get_businessPlaces', [DropdownController::class, 'getBusinessPlaces']);
Route::get('/get_taxRate', [DropdownController::class, 'getTaxRate']);
Route::get('/get_vendor_gstno', [DropdownController::class, 'getVendorGst']);
Route::get('/get_gstno', [DropdownController::class, 'getGstNumbers']);
Route::get('/get_financial_years', [DropdownController::class, 'getFinancialYears']);
Route::get('/get_months', [DropdownController::class, 'getMonths']);
// below Routes have No tables
Route::get('/get_pos', [DropdownController::class, 'getPOS']);
Route::get('/get_taxtype', [DropdownController::class, 'getTaxTypes']);
Route::get('/get_delayed_submission', [DropdownController::class, 'reasonForDelayedSubmission']);
Route::get('/vendor_type', [DropdownController::class, 'getVendorType']);

// Theme 
Route::post('/update_theme', [WebdataController::class, 'postThemeColor']);
Route::post('/get_theme', [WebdataController::class, 'getThemeColor']);
// GSTR2A reports
Route::post('/gstr2a/check', [Gstr2aMismatchController::class, 'checkMismatch']);
//  Invoice/po Report for Download screen
Route::post('/report/financialyear_wise', [InvoiceReportController::class, 'reportFinancialyearWise']);
Route::post('/report/rejected_invoices', [InvoiceReportController::class, 'reportRejectedInvoices']);
Route::post('/report/gstr2a_mismatch', [InvoiceReportController::class, 'reportGstr2aMismatch']);
Route::post('/report/open_po', [InvoiceReportController::class, 'reportOpenPO']);
// Search Report
Route::post('/report/invoice_search', [InvoiceController::class, 'invoiceSearch']);


// Route::post('/get_reports',[])
// Route::post('/admin/logout', [UserController::class, 'logout'])->middleware('auth:admin');



// =================================>   PurchaseOrder Screen   <=======================

Route::post('/create_purchase_order', [PurchaseOrderController::class, "postPurchaseOrder"]);
Route::post('/po_filter', [PurchaseOrderController::class, "getPurchaseOrdersList"]);
//------- milestone
Route::post('/add_milestone', [MilestoneController::class, "createMilestone"]);
Route::post('/get_milestones', [MilestoneController::class, "getMilestones"]);
Route::post('/update_milestone', [MilestoneController::class, "updateMilestone"]);
