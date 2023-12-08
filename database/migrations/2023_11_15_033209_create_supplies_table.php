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
        Schema::create('supplies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('sodonhang')->nullable(false);
            $table->string('nhacungcap')->nullable(false);
            $table->string('chiphi')->nullable(false);
            $table->string('noidungphancum')->nullable();
            $table->integer('stt')->nullable(false);
            $table->string('tenvattu')->nullable(false);
            $table->string('maso')->nullable(false);
            $table->string('donvitinh')->nullable(false);
            $table->integer('soluong')->nullable(false);
            $table->dateTime('ngaynhan');
            $table->string('note')->nullable();
            $table->integer('status')->default(0);
            $table->string('barcode')->nullable();
            $table->foreign('project_id')
                  ->references('id')
                  ->on('projects')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplies');
    }
};
