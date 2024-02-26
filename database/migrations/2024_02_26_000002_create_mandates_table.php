<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gocardless_mandates', function (Blueprint $table) {
            $table->string('id', 50)->primary()->index();
            $table->string('customer_id', 50)->index();
            $table->string('creditor_id', 50)->nullable()->index();
            $table->string('customer_bank_account_id', 50)->nullable()->index();
            $table->string('status', 50)->index();
            $table->string('reference', 250)->nullable();

            $table->json('data')->nullable()->comment('Latest received mandate data.');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gocardless_mandates');
    }
};
