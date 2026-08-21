<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employer_profiles', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('company_name');
            $table->string('industry')->nullable();
            $table->string('location')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->text('description')->nullable();
            $table->string('logo_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('employer_profiles', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropUnique(['employer_profiles_user_id_unique']);

            $table->dropColumn([
                'user_id',
                'company_name',
                'industry',
                'location',
                'phone',
                'website',
                'description',
                'logo_path',
            ]);
        });
    }
};
