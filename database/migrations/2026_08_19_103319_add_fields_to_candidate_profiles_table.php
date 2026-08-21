<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidate_profiles', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('full_name');
            $table->string('phone')->nullable();
            $table->string('location')->nullable();
            $table->string('job_title')->nullable();
            $table->text('skills')->nullable();
            $table->text('education')->nullable();
            $table->text('experience')->nullable();
            $table->string('cv_path')->nullable();
            $table->text('bio')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('candidate_profiles', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropUnique(['candidate_profiles_user_id_unique']);

            $table->dropColumn([
                'user_id',
                'full_name',
                'phone',
                'location',
                'job_title',
                'skills',
                'education',
                'experience',
                'cv_path',
                'bio',
            ]);
        });
    }
};
