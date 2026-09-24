<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('public_site_pages')) {
            Schema::create('public_site_pages', function (Blueprint $table) {
                $table->id();
                $table->string('tenant_id');
                $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
                $table->string('title');
                $table->string('slug');
                $table->longText('content')->nullable();
                $table->string('meta_description')->nullable();
                $table->boolean('is_published')->default(false);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
                $table->softDeletes();

                $table->index(['tenant_id', 'is_published']);
                $table->unique(['tenant_id', 'slug']);
            });
        }

        if (!Schema::hasTable('public_site_news')) {
            Schema::create('public_site_news', function (Blueprint $table) {
                $table->id();
                $table->string('tenant_id');
                $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
                $table->string('title');
                $table->string('slug');
                $table->text('excerpt')->nullable();
                $table->longText('content')->nullable();
                $table->string('image_path')->nullable();
                $table->boolean('is_published')->default(false);
                $table->date('published_at')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['tenant_id', 'is_published', 'published_at']);
                $table->unique(['tenant_id', 'slug']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('public_site_news');
        Schema::dropIfExists('public_site_pages');
    }
};
