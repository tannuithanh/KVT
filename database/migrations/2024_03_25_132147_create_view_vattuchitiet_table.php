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
            $table->unsignedBigInteger('supply_id')->unique();
            $table->integer('soluongnhapkho');
            $table->integer('soluongdatchatluong')->nullable();
            $table->integer('status')->default(0);
            $table->timestamps();
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
