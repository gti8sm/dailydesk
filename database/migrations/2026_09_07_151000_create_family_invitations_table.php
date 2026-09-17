<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('family_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_id')->constrained()->onDelete('cascade');
            $table->string('email');
            $table->string('token')->unique();
            $table->enum('status', ['pending', 'accepted', 'expired'])->default('pending');
            $table->datetime('expires_at')->nullable();
            $table->datetime('accepted_at')->nullable();
            $table->timestamps();

            $table->index(['family_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_invitations');
    }
};
