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
      Schema::create('tbl_vpit_fin', function (Blueprint $table) {
    $table->id();
    $table->string('nobkt');
    $table->string('vendor');
    $table->integer('amount');
    $table->string('invoice');
    $table->string('file');
    $table->date('received_date');
    $table->date('payment_date');
    $table->string('payment_invoice');
    $table->string('is_status')->default('pending');
    $table->datetime('created');
    $table->string('created_by');
    $table->datetime('updated')->nullable();
    $table->string('updated_by')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vpit_fins');
    }
};
