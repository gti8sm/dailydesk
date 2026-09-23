<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Renommer tenant_id en owner_tenant_id sur schools
        if (Schema::hasTable('schools')) {
            if (Schema::hasColumn('schools', 'tenant_id') && !Schema::hasColumn('schools', 'owner_tenant_id')) {
                Schema::table('schools', function (Blueprint $table) {
                    $table->renameColumn('tenant_id', 'owner_tenant_id');
                });
            }
            if (!Schema::hasColumn('schools', 'intercommunality_id')) {
                Schema::table('schools', function (Blueprint $table) {
                    $table->foreignId('intercommunality_id')->nullable()->after('owner_tenant_id')->constrained('intercommunalities')->nullOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('schools')) {
            if (Schema::hasColumn('schools', 'intercommunality_id')) {
                Schema::table('schools', function (Blueprint $table) {
                    $table->dropConstrainedForeignId('intercommunality_id');
                });
            }
            if (Schema::hasColumn('schools', 'owner_tenant_id') && !Schema::hasColumn('schools', 'tenant_id')) {
                Schema::table('schools', function (Blueprint $table) {
                    $table->renameColumn('owner_tenant_id', 'tenant_id');
                });
            }
        }
    }
};
