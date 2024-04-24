<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('catalog_id');
            $table->foreign('catalog_id')->references('id')->on('catalogs')->onDelete('cascade');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null')->nullable();
            $table->string('tenvattu');
            $table->string('maso');
            $table->string('donvitinh');
            $table->integer('soluong');
            $table->decimal('don_gia', 8, 2)->nullable();  // Đơn giá có thể null, với 8 chữ số, 2 chữ số sau dấu phẩy
            $table->decimal('thanh_tien', 10, 2)->nullable();  // Thành tiền, có thể null, với 10 chữ số, 2 chữ số sau dấu phẩy
            $table->string('exportdrawings')->nullable();
            $table->string('note')->nullable();
            $table->integer('status')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplies');
    }
};
