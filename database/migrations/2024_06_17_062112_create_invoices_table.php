<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string("vendor_id");
            $table->string("vendor_invoice_no")->unique();
            $table->string("currency");
            $table->integer("gross_amount");
            $table->string("tax_amount");
            $table->string("taxable_value");

            $table->string("business_place");
            $table->string("tax_rate");
            $table->string("invoice_date");
            $table->string("vendor_code");
            $table->string("vendor_gst_no");

            $table->string("gst_no");
            $table->string("pos");
            $table->string("tax_type");
            $table->string("requester_name");
            $table->string("lut_number");

            $table->date("lut_expire_date");
            $table->string("eway_bill_no");
            $table->string("reason_delayed_submission");
            $table->string("tcs");
            $table->string("vendor_type");
            $table->longText("vendor_remarks");
            $table->longText("client_remarks");
            $table->string("invoice_image");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
