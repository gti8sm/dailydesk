<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('children', function (Blueprint $table) {
            $table->foreignId('class_id')->nullable()->after('class')->constrained('school_classes')->nullOnDelete();
            $table->boolean('garderie_subscribed')->default(false)->after('is_active');
            $table->boolean('cantine_subscribed')->default(false)->after('garderie_subscribed');
        });
    }

    public function down(): void
    {
        Schema::table('children', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropColumn(['class_id', 'garderie_subscribed', 'cantine_subscribed']);
        });
    }
};
