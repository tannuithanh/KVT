<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('order_supply', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('supply_id');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('supply_id')->references('id')->on('supplies')->onDelete('cascade');
            $table->integer('soluong');
            $table->timestamps();

            // Đảm bảo mỗi đơn hàng chỉ xuất hiện một lần với mỗi vật tư
            $table->unique(['order_id', 'supply_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_supply');
    }
};
