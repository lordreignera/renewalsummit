<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->string('organization', 100)->nullable()->after('affiliation');
            $table->string('organization_other', 200)->nullable()->after('organization');
            $table->boolean('is_group')->default(false)->after('organization_other');
            $table->string('group_name', 200)->nullable()->after('is_group');
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn(['organization', 'organization_other', 'is_group', 'group_name']);
        });
    }
};
