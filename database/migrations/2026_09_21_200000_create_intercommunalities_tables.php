<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('intercommunalities')) {
            Schema::create('intercommunalities', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('siren')->nullable();
                $table->string('type')->default('communaute_communes'); // communaute_communes, communaute_agglomeration, syndicat, epci
                $table->string('address')->nullable();
                $table->string('city')->nullable();
                $table->string('postal_code')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('tenant_intercommunality')) {
            Schema::create('tenant_intercommunality', function (Blueprint $table) {
                $table->uuid('tenant_id');
                $table->foreignId('intercommunality_id')->constrained()->cascadeOnDelete();
                $table->timestamp('joined_at')->useCurrent();
                $table->primary(['tenant_id', 'intercommunality_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_intercommunality');
        Schema::dropIfExists('intercommunalities');
    }
};
