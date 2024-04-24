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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên
            $table->text('description')->nullable(); // Mô tả
            $table->string('telephone')->nullable(); // Tel
            $table->string('fax')->nullable(); // Fax
            $table->string('tax_number')->nullable(); // MST (Mã số thuế)
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
