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
        Schema::create('cantine_menus', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->date('menu_date');
            $table->enum('meal_type', ['lunch', 'snack'])->default('lunch');
            $table->string('title')->nullable();
            $table->string('starter')->nullable();
            $table->string('main_course');
            $table->string('side_dish')->nullable();
            $table->string('dessert')->nullable();
            $table->json('allergens')->nullable();
            $table->boolean('vegetarian')->default(false);
            $table->text('notes')->nullable();
            $table->boolean('is_published')->default(false);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->unique(['tenant_id', 'menu_date', 'meal_type'], 'cantine_menus_unique');
            $table->index(['tenant_id', 'menu_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cantine_menus');
    }
};
