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
        Schema::create('garderie_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->date('event_date');
            $table->time('event_time')->nullable();
            $table->enum('event_type', ['incident', 'accident', 'behavior', 'medical', 'other'])->default('other');
            $table->string('title');
            $table->text('description');
            $table->enum('severity', ['low', 'medium', 'high'])->default('low');
            $table->boolean('parents_notified')->default(false);
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();
            
            $table->index(['child_id', 'event_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('garderie_events');
    }
};
