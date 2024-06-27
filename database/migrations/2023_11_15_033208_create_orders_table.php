<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('catalog_id');
            $table->foreign('catalog_id')->references('id')->on('catalogs')->onDelete('cascade');
            $table->unsignedBigInteger('expense_id');  // Sử dụng expense_id thay vì chiphi
            $table->foreign('expense_id')->references('id')->on('expenses')->onDelete('cascade');
            $table->string('sodonhang');
            $table->string('nhacungcap')->nullable();
            $table->string('noidung')->nullable();
            $table->date('ngayhoanthanh');
            $table->string('ghichu')->nullable();
            $table->string('excel_file')->nullable();  // Cột mới để lưu tên file Excel
            $table->unsignedBigInteger('status')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
