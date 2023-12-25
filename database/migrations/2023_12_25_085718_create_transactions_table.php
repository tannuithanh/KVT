<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supply_id');
            $table->integer('soluong');
            $table->string('loaigiaodich'); // Nhập hoặc Xuất
            $table->dateTime('ngaygiaodich');
            $table->string('ghichu')->nullable();
            // Các cột khác mà bạn muốn thêm
            $table->timestamps();

            $table->foreign('supply_id')
                  ->references('id')
                  ->on('supplies')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
