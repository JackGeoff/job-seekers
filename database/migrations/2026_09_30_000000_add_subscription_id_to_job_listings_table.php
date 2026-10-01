<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('job_listings', 'subscription_id')) {
            Schema::table('job_listings', function (Blueprint $table) {
                $table->foreignId('subscription_id')
                    ->nullable()
                    ->after('employer_profile_id')
                    ->constrained('employer_subscriptions')
                    ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('job_listings', 'subscription_id')) {
            Schema::table('job_listings', function (Blueprint $table) {
                $table->dropForeign(['subscription_id']);
                $table->dropColumn('subscription_id');
            });
        }
    }
};