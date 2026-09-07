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
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->string('slug')->unique()->after('name');
            $table->string('email')->nullable()->after('slug');
            $table->string('phone')->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->string('city')->nullable()->after('address');
            $table->string('postal_code')->nullable()->after('city');
            $table->string('logo_path')->nullable()->after('postal_code');
            $table->string('primary_color')->default('#3B82F6')->after('logo_path');
            $table->string('secondary_color')->default('#6366F1')->after('primary_color');
            $table->enum('status', ['active', 'suspended', 'trial', 'expired'])->default('trial')->after('secondary_color');
            $table->string('subscription_plan')->default('starter')->after('status');
            $table->timestamp('subscription_starts_at')->nullable()->after('subscription_plan');
            $table->timestamp('subscription_expires_at')->nullable()->after('subscription_starts_at');
            $table->timestamp('trial_ends_at')->nullable()->after('subscription_expires_at');
            $table->integer('max_children')->default(50)->after('trial_ends_at');
            $table->json('modules_enabled')->nullable()->after('max_children');
            $table->json('settings')->nullable()->after('modules_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'name', 'slug', 'email', 'phone', 'address', 'city', 'postal_code',
                'logo_path', 'primary_color', 'secondary_color', 'status',
                'subscription_plan', 'subscription_starts_at', 'subscription_expires_at',
                'trial_ends_at', 'max_children', 'modules_enabled', 'settings'
            ]);
        });
    }
};
