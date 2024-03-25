<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('view_vattuchitiet', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supply_id');
            $table->integer('soluongnhapkho');
            $table->integer('soluongdatchatluong');
            $table->integer('status')->default(0); // Đảm bảo rằng giá trị mặc định là số 0 thay vì chuỗi '0'
            $table->timestamps();

            // Tạo khóa ngoại tham chiếu đến bảng supplies và xử lý khi xóa (onDelete cascade)
            $table->foreign('supply_id')
                  ->references('id')
                  ->on('supplies')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('view_vattuchitiet');
    }
};
