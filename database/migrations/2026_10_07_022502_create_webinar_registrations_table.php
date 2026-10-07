<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webinar_registrations', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email')->index();
            $table->string('phone')->nullable();
            $table->string('career_stage')->nullable();
            $table->string('linkedin_url')->nullable();

            $table->boolean('marketing_consent')->default(false);

            $table->timestamps();

            $table->unique(['email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webinar_registrations');
    }
};