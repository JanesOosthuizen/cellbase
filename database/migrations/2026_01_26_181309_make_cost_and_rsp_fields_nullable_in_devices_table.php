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
        Schema::table('devices', function (Blueprint $table) {
            $table->decimal('cost_excl', 10, 2)->nullable()->change();
            $table->decimal('cost_incl', 10, 2)->nullable()->change();
            $table->decimal('rsp_excl', 10, 2)->nullable()->change();
            $table->decimal('rsp_incl', 10, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->decimal('cost_excl', 10, 2)->nullable(false)->change();
            $table->decimal('cost_incl', 10, 2)->nullable(false)->change();
            $table->decimal('rsp_excl', 10, 2)->nullable(false)->change();
            $table->decimal('rsp_incl', 10, 2)->nullable(false)->change();
        });
    }
};
