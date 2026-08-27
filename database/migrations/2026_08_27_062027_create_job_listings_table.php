<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_listings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employer_profile_id')
                ->constrained('employer_profiles')
                ->cascadeOnDelete();

            $table->string('title');
            $table->text('description');
            $table->string('category');
            $table->string('location');
            $table->string('employment_type');

            $table->decimal('salary_min', 12, 2)->nullable();
            $table->decimal('salary_max', 12, 2)->nullable();
            $table->string('salary_currency', 3)->default('KES');

            $table->date('application_deadline')->nullable();

            $table->enum('status', [
                'draft',
                'published',
                'closed',
            ])->default('draft');

            $table->timestamps();

            $table->index('status');
            $table->index('category');
            $table->index('location');
            $table->index('application_deadline');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_listings');
    }
};
