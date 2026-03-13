<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained('registrations')->cascadeOnDelete();
            $table->string('swapp_transaction_id')->nullable()->unique();
            $table->string('swapp_reference')->nullable();
            $table->enum('payment_method', ['mobile_money', 'visa', 'other'])->default('mobile_money');
            $table->string('phone_number')->nullable();    // for MM
            $table->string('network')->nullable();         // MTN / Airtel
            $table->unsignedBigInteger('amount');          // UGX
            $table->string('currency')->default('UGX');
            $table->enum('status', [
                'initiated',
                'pending',
                'success',
                'failed',
                'cancelled',
            ])->default('initiated');
            $table->json('swapp_response')->nullable();    // raw gateway response
            $table->string('failure_reason')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
