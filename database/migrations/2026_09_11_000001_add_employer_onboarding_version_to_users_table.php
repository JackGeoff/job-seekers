<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Existing accounts are explicitly marked as pre-paid-onboarding (version 0).
            $table->unsignedTinyInteger('employer_onboarding_version')->default(0)->after('account_type');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('employer_onboarding_version');
        });
    }
};
