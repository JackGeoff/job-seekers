<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('job_id')
                ->constrained('job_listings')
                ->cascadeOnDelete();

            $table->foreignId('candidate_profile_id')
                ->constrained('candidate_profiles')
                ->cascadeOnDelete();

            $table->string('status')->default('submitted');

            $table->timestamps();

            $table->unique([
                'job_id',
                'candidate_profile_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
