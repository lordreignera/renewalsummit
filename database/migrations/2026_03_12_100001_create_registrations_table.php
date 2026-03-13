<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique(); // unique reg reference e.g. RS2026-00001
            $table->string('qr_token')->unique()->nullable();

            // Step 1 – Personal Info
            $table->string('full_name');
            $table->enum('designation', [
                'fcc_regional_leader',
                'senior_pastor',
                'church_leader',
                'corporate',
            ])->default('senior_pastor');
            $table->string('designation_specify')->nullable(); // For church_leader – please specify
            $table->string('phone')->unique();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->enum('country_type', ['local', 'africa', 'international'])->default('local');
            $table->string('nationality')->nullable();

            // Step 2 – Church affiliation
            $table->enum('affiliation', ['fcc', 'other'])->default('other');
            $table->string('fcc_region')->nullable();           // e.g. East Africa Region
            $table->string('fcc_regional_leader')->nullable();  // name of their FCC regional leader
            $table->string('fcc_church')->nullable();
            $table->string('fcc_pastor')->nullable();

            // Fees
            $table->string('currency', 3)->default('UGX');      // UGX or USD
            $table->unsignedBigInteger('base_fee')->default(0); // in currency units
            $table->unsignedBigInteger('total_amount')->default(0);

            // Multi-step form progress
            $table->tinyInteger('current_step')->default(1); // 1,2,3
            $table->enum('status', [
                'draft',       // form started
                'pending',     // awaiting payment
                'paid',        // payment confirmed
                'checked_in',  // scanned at event
                'cancelled',
            ])->default('draft');

            // QR / confirmation
            $table->string('qr_code_path')->nullable();
            $table->timestamp('qr_sent_at')->nullable();
            $table->timestamp('checked_in_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
