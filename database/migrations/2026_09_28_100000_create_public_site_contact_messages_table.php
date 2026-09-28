<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('public_site_contact_messages')) {
            Schema::create('public_site_contact_messages', function (Blueprint $table) {
                $table->id();
                $table->string('tenant_id');
                $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
                $table->string('name');
                $table->string('email');
                $table->string('phone')->nullable();
                $table->string('subject')->nullable();
                $table->text('message');
                $table->string('ip_address', 45)->nullable();
                $table->boolean('is_read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'is_read', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('public_site_contact_messages');
    }
};
