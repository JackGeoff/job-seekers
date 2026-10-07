<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employer_payment_orders', function (Blueprint $table) {
            $table->string('currency', 3)->default('KES');
            $table->string('paystack_reference', 64)->nullable()->unique();
            $table->string('paystack_access_code')->nullable();
            $table->text('paystack_authorization_url')->nullable();
            $table->string('paystack_phone', 20)->nullable();
            $table->text('paystack_display_text')->nullable();
            $table->string('paystack_status', 32)->nullable();
            $table->timestamp('paystack_initiated_at')->nullable();
            $table->foreignId('subscription_id')
                ->nullable()
                ->constrained('employer_subscriptions')
                ->nullOnDelete();

            $table->index(['status', 'paystack_initiated_at']);
        });
    }

    public function down(): void
    {
        Schema::table('employer_payment_orders', function (Blueprint $table) {
            $table->dropForeign(['subscription_id']);
            $table->dropIndex(['status', 'paystack_initiated_at']);
            $table->dropUnique(['paystack_reference']);
            $table->dropColumn([
                'currency',
                'paystack_reference',
                'paystack_access_code',
                'paystack_authorization_url',
                'paystack_phone',
                'paystack_display_text',
                'paystack_status',
                'paystack_initiated_at',
                'subscription_id',
            ]);
        });
    }
};