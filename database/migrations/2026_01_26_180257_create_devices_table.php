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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('product_code');
            $table->string('bar_code')->nullable();
            $table->foreignId('manufacturer_id')->constrained()->onDelete('cascade');
            $table->string('model');
            $table->decimal('cost_excl', 10, 2);
            $table->decimal('cost_incl', 10, 2);
            $table->decimal('rsp_excl', 10, 2);
            $table->decimal('rsp_incl', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
