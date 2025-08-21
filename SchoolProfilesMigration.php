<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('school_profiles', function (Blueprint $table) {
            $table->string('address', 500)->nullable()->after('academic_year');
            $table->string('phone', 50)->nullable()->after('address');
            $table->string('email', 255)->nullable()->after('phone');
            $table->string('website', 255)->nullable()->after('email');
            $table->text('about')->nullable()->after('website');
            $table->integer('established_year')->nullable()->after('about');
            $table->string('banner_image_path', 1024)->nullable()->after('logo_path');
        });
    }

    public function down(): void
    {
        Schema::table('school_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'address',
                'phone',
                'email',
                'website',
                'about',
                'established_year',
                'banner_image_path',
            ]);
        });
    }
};
