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
        Schema::create('imeis', function (Blueprint $table) {
            $table->integer('imeiID', true)->primary();
            $table->date('date')->nullable();
            $table->string('invoice', 45)->nullable();
            $table->string('invoiceId', 255);
            $table->string('phone', 255)->nullable();
            $table->string('phone_stock_code', 11)->nullable();
            $table->string('imei', 255)->unique()->nullable();
            $table->integer('nonImei')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('allocatedTo', 255)->nullable();
            $table->string('number', 255)->nullable();
            $table->string('name', 255)->nullable();
            $table->date('activationDate')->nullable();
            $table->string('DealSheetNr', 45)->nullable();
            $table->string('upgradeContract', 45)->nullable();
            $table->integer('company')->nullable();
            $table->datetime('entryAddedDate')->nullable();
            $table->datetime('entryModifiedDate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imeis');
    }
};
