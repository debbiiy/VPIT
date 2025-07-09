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
        Schema::create('tbl_vpit_doc', function (Blueprint $table) {
        $table->id();
        $table->string('jo_code');
        $table->string('container_no');
        $table->string('file');
        $table->string('is_status')->default('pending');
        $table->string('vendor');
        $table->datetime('created');
        $table->string('created_by');
        $table->datetime('approved')->nullable();
        $table->string('approved_by')->nullable();
        $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vpit_docs');
    }
};
