<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->string('emergency_contact_name')->nullable()->after('address');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');

            $table->text('medical_conditions')->nullable()->after('emergency_contact_phone');
            $table->text('allergies')->nullable()->after('medical_conditions');
            $table->text('mobility_needs')->nullable()->after('allergies');
            $table->text('special_needs')->nullable()->after('mobility_needs');

            $table->boolean('accommodation_required')->default(false)->after('special_needs');
            $table->string('accommodation_choice')->nullable()->after('accommodation_required');
            $table->integer('accommodation_fee')->nullable()->after('accommodation_choice');

            $table->boolean('sms_opt_in')->default(false)->after('accommodation_fee');
            $table->text('admin_notes')->nullable()->after('sms_opt_in');
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn([
                'emergency_contact_name', 'emergency_contact_phone',
                'medical_conditions', 'allergies', 'mobility_needs', 'special_needs',
                'accommodation_required', 'accommodation_choice', 'accommodation_fee',
                'sms_opt_in', 'admin_notes'
            ]);
        });
    }
};
