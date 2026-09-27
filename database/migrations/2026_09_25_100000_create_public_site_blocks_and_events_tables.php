<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('public_site_blocks')) {
            Schema::create('public_site_blocks', function (Blueprint $table) {
                $table->id();
                $table->string('tenant_id');
                $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
                $table->foreignId('page_id')->nullable()->constrained('public_site_pages')->cascadeOnDelete();
                $table->string('block_type');
                $table->string('title')->nullable();
                $table->json('config')->nullable();
                $table->longText('grapesjs_project')->nullable();
                $table->longText('content_html')->nullable();
                $table->longText('content_css')->nullable();
                $table->string('width')->default('full');
                $table->boolean('is_published')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->index(['tenant_id', 'page_id', 'sort_order']);
            });
        }

        if (!Schema::hasTable('public_site_events')) {
            Schema::create('public_site_events', function (Blueprint $table) {
                $table->id();
                $table->string('tenant_id');
                $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
                $table->string('title');
                $table->string('slug');
                $table->text('description')->nullable();
                $table->string('location')->nullable();
                $table->date('starts_at');
                $table->date('ends_at')->nullable();
                $table->string('image_path')->nullable();
                $table->boolean('is_published')->default(false);
                $table->timestamps();

                $table->index(['tenant_id', 'is_published', 'starts_at']);
                $table->unique(['tenant_id', 'slug']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('public_site_events');
        Schema::dropIfExists('public_site_blocks');
    }
};
