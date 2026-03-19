<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            if (! Schema::hasColumn('registrations', 'emergency_contact_name')) {
                $table->string('emergency_contact_name')->nullable()->after('fcc_pastor');
            }
            if (! Schema::hasColumn('registrations', 'emergency_contact_phone')) {
                $table->string('emergency_contact_phone', 50)->nullable()->after('emergency_contact_name');
            }
            if (! Schema::hasColumn('registrations', 'medical_conditions')) {
                $table->text('medical_conditions')->nullable()->after('emergency_contact_phone');
            }
            if (! Schema::hasColumn('registrations', 'allergies')) {
                $table->text('allergies')->nullable()->after('medical_conditions');
            }
            if (! Schema::hasColumn('registrations', 'mobility_needs')) {
                $table->text('mobility_needs')->nullable()->after('allergies');
            }
            if (! Schema::hasColumn('registrations', 'special_needs')) {
                $table->text('special_needs')->nullable()->after('mobility_needs');
            }
            if (! Schema::hasColumn('registrations', 'accommodation_required')) {
                $table->boolean('accommodation_required')->default(false)->after('special_needs');
            }
            if (! Schema::hasColumn('registrations', 'accommodation_choice')) {
                $table->string('accommodation_choice')->nullable()->after('accommodation_required');
            }
            if (! Schema::hasColumn('registrations', 'accommodation_fee')) {
                $table->unsignedBigInteger('accommodation_fee')->default(0)->after('accommodation_choice');
            }
            if (! Schema::hasColumn('registrations', 'sms_opt_in')) {
                $table->boolean('sms_opt_in')->default(false)->after('accommodation_fee');
            }
            if (! Schema::hasColumn('registrations', 'admin_notes')) {
                $table->text('admin_notes')->nullable()->after('sms_opt_in');
            }
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $drop = [];

            foreach ([
                'emergency_contact_name',
                'emergency_contact_phone',
                'medical_conditions',
                'allergies',
                'mobility_needs',
                'special_needs',
                'accommodation_required',
                'accommodation_choice',
                'accommodation_fee',
                'sms_opt_in',
                'admin_notes',
            ] as $column) {
                if (Schema::hasColumn('registrations', $column)) {
                    $drop[] = $column;
                }
            }

            if (! empty($drop)) {
                $table->dropColumn($drop);
            }
        });
    }
};
