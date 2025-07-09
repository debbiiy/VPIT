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
        Schema::create('tbl_vpit_fin_detail', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('vpit_fin_id');
        $table->string('nobkt');
        $table->string('jo_code');
        $table->string('container_no');
        $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vpit_fin_details');
    }
};
