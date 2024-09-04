<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->integer("service_provider");
            $table->integer("requester_id");
            $table->string("po_number")->unique();
            $table->longText("line_item_description");
            $table->integer("quantity");
            $table->double("rate");
            $table->double("amount");
            $table->string("hsn");
            $table->float("tax_rate");
            $table->double("tax_amount");
            $table->string("file_path");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchare_orders');
    }
};
