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
        Schema::create('repairs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('phone')->nullable();
            $table->string('imei', 20)->nullable();
            $table->string('cell_nr')->nullable();
            $table->string('contact_nr')->nullable();
            $table->foreignId('allocated_to')->nullable()->constrained('external_users')->nullOnDelete();
            $table->text('fault_description')->nullable();
            $table->string('ticket_status', 50)->default('booked_in');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repairs');
    }
};
