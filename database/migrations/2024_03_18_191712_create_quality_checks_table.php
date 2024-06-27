<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('quality_checks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supply_id');
            $table->integer('soluongnhapkho');
            $table->integer('soluongdatchatluong')->nullable();
            $table->integer('status')->default('0');
            $table->text('note')->nullable();
            $table->date('ngaykiemtra')->nullable();
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
