<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('parents', 'address')) {
        Schema::table('parents', function (Blueprint $table) {
            $table->string('address')->nullable()->after('mobile');
            $table->string('postal_code')->nullable()->after('address');
            $table->string('city')->nullable()->after('postal_code');
            $table->boolean('is_legal_guardian')->default(false)->after('can_pickup');
        });
        }
    }

    public function down(): void
    {
        Schema::table('parents', function (Blueprint $table) {
            $table->dropColumn(['address', 'postal_code', 'city', 'is_legal_guardian']);
        });
    }
};
