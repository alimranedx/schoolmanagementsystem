<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('school_profiles', 'country')) {
                if (Schema::hasColumn('school_profiles', 'academic_year')) {
                    $table->string('country')->nullable()->after('academic_year');
                } else {
                    $table->string('country')->nullable();
                }
            }
            if (!Schema::hasColumn('school_profiles', 'district')) {
                if (Schema::hasColumn('school_profiles', 'country')) {
                    $table->string('district')->nullable()->after('country');
                } else {
                    $table->string('district')->nullable();
                }
            }
            if (!Schema::hasColumn('school_profiles', 'upazila')) {
                if (Schema::hasColumn('school_profiles', 'district')) {
                    $table->string('upazila')->nullable()->after('district');
                } else {
                    $table->string('upazila')->nullable();
                }
            }
            if (!Schema::hasColumn('school_profiles', 'timezone')) {
                if (Schema::hasColumn('school_profiles', 'upazila')) {
                    $table->string('timezone')->nullable()->after('upazila');
                } else {
                    $table->string('timezone')->nullable();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('school_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('school_profiles', 'timezone')) {
                $table->dropColumn('timezone');
            }
            if (Schema::hasColumn('school_profiles', 'upazila')) {
                $table->dropColumn('upazila');
            }
            if (Schema::hasColumn('school_profiles', 'district')) {
                $table->dropColumn('district');
            }
            if (Schema::hasColumn('school_profiles', 'country')) {
                $table->dropColumn('country');
            }
        });
    }
};
