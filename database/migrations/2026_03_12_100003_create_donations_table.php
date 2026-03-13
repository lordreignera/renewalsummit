<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->string('donor_name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->unsignedBigInteger('amount'); // UGX
            $table->string('currency')->default('UGX');
            $table->enum('payment_method', ['mobile_money', 'visa', 'other'])->default('mobile_money');
            $table->string('network')->nullable();
            $table->string('swapp_transaction_id')->nullable()->unique();
            $table->json('swapp_response')->nullable();
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->text('message')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
