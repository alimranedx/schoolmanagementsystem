<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('name');
            $table->enum('type', ['govt', 'school'])->default('school');
            $table->string('country')->nullable();
            $table->integer('year')->index();
            $table->timestamps();

            $table->unique(['date','name','country']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};
