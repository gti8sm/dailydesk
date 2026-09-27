<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('public_site_events') && !Schema::hasColumn('public_site_events', 'recurrence_type')) {
            Schema::table('public_site_events', function (Blueprint $table) {
                $table->string('recurrence_type')->default('none')->after('ends_at');
                $table->unsignedSmallInteger('recurrence_interval')->default(1)->after('recurrence_type');
                $table->date('recurrence_end_date')->nullable()->after('recurrence_interval');
                $table->index(['tenant_id', 'recurrence_type', 'starts_at']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('public_site_events') && Schema::hasColumn('public_site_events', 'recurrence_type')) {
            Schema::table('public_site_events', function (Blueprint $table) {
                $table->dropIndex(['tenant_id', 'recurrence_type', 'starts_at']);
                $table->dropColumn(['recurrence_type', 'recurrence_interval', 'recurrence_end_date']);
            });
        }
    }
};
