<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('stock_locations')) {
        Schema::create('stock_locations', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index(['tenant_id', 'is_active']);
        });
        }

        if (!Schema::hasTable('stock_items')) {
        Schema::create('stock_items', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('location_id')->constrained('stock_locations')->onDelete('cascade');
            $table->string('name');
            $table->string('reference')->nullable();
            $table->string('category')->nullable();
            $table->string('unit')->default('pièce');
            $table->decimal('quantity', 10, 2)->default(0);
            $table->decimal('min_quantity', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index(['tenant_id', 'location_id']);
            $table->index(['tenant_id', 'is_active']);
        });
        }

        if (!Schema::hasTable('stock_movements')) {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('stock_item_id')->constrained('stock_items')->onDelete('cascade');
            $table->enum('type', ['in', 'out', 'adjust'])->default('in');
            $table->decimal('quantity', 10, 2);
            $table->string('reason')->nullable();
            $table->decimal('new_quantity', 10, 2);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index(['tenant_id', 'stock_item_id']);
            $table->index(['tenant_id', 'created_at']);
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('stock_items');
        Schema::dropIfExists('stock_locations');
    }
};
