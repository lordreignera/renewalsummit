<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            if (! Schema::hasColumn('registrations', 'accommodation_payment_status')) {
                $table->enum('accommodation_payment_status', ['not_required', 'pending', 'paid'])
                    ->default('not_required')
                    ->after('accommodation_fee');
            }
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            if (Schema::hasColumn('registrations', 'accommodation_payment_status')) {
                $table->dropColumn('accommodation_payment_status');
            }
        });
    }
};
