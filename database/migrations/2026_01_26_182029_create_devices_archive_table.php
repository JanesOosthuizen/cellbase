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
        Schema::create('devices_archive', function (Blueprint $table) {
            $table->id();
            $table->string('product_code');
            $table->string('bar_code')->nullable();
            $table->foreignId('manufacturer_id')->nullable()->constrained()->onDelete('set null');
            $table->string('model');
            $table->decimal('cost_excl', 10, 2)->nullable();
            $table->decimal('cost_incl', 10, 2)->nullable();
            $table->decimal('rsp_excl', 10, 2)->nullable();
            $table->decimal('rsp_incl', 10, 2)->nullable();
            $table->string('batch_number');
            $table->timestamps();
            
            $table->index('batch_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices_archive');
    }
};
