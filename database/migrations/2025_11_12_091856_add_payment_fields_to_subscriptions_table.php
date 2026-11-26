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
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('status');
            $table->string('subscription_id')->nullable()->after('payment_method');
            $table->text('billing_info')->nullable()->after('subscription_id');

            $table->index('subscription_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex(['subscription_id']);
            $table->dropColumn(['payment_method', 'subscription_id', 'billing_info']);
        });
    }
};
