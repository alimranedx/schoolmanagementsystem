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
            if (!Schema::hasColumn('school_profiles', 'timezone')) {
                if (Schema::hasColumn('school_profiles', 'country')) {
                    $table->string('timezone')->nullable()->after('country');
                } else {
                    $table->string('timezone')->nullable();
                }
            }
            if (!Schema::hasColumn('school_profiles', 'address')) {
                $table->string('address')->nullable()->after('logo_path');
            }
            if (!Schema::hasColumn('school_profiles', 'phone')) {
                $table->string('phone')->nullable()->after('address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('school_profiles', function (Blueprint $table) {
            $drops = [];
            foreach (['country','timezone','address','phone'] as $col) {
                if (Schema::hasColumn('school_profiles', $col)) $drops[] = $col;
            }
            if (!empty($drops)) {
                $table->dropColumn($drops);
            }
        });
    }
};
