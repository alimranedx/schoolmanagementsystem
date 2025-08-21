<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_profiles', function (Blueprint $table) {
            // New fields
            $table->dateTime('established_at')->nullable()->after('timezone');
            $table->string('district')->nullable()->after('country');
            $table->string('upazila')->nullable()->after('district');

            // Remove academic_year per new requirements
            if (Schema::hasColumn('school_profiles', 'academic_year')) {
                $table->dropColumn('academic_year');
            }
        });
    }

    public function down(): void
    {
        Schema::table('school_profiles', function (Blueprint $table) {
            // Recreate academic_year
            if (!Schema::hasColumn('school_profiles', 'academic_year')) {
                $table->string('academic_year')->nullable()->after('logo_path');
            }
            // Drop newly added fields
            if (Schema::hasColumn('school_profiles', 'upazila')) {
                $table->dropColumn('upazila');
            }
            if (Schema::hasColumn('school_profiles', 'district')) {
                $table->dropColumn('district');
            }
            if (Schema::hasColumn('school_profiles', 'established_at')) {
                $table->dropColumn('established_at');
            }
        });
    }
};
