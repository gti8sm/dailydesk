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
        Schema::create('garderie_presences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->time('arrival_time')->nullable();
            $table->time('departure_time')->nullable();
            $table->foreignId('recorded_by_arrival')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('recorded_by_departure')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('duration_minutes')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['child_id', 'date']);
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('garderie_presences');
    }
};
