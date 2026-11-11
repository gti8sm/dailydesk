<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'families',
            'children',
            'parents',
            'school_classes',
            'garderie_presences',
            'garderie_events',
            'cantine_presences',
            'cantine_events',
            'settings',
            'family_invitations',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'tenant_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->string('tenant_id')->nullable()->after('id');
                    $table->index('tenant_id');
                });
            }
        }

        // Users: add tenant_id + fix unique constraint
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'tenant_id')) {
            // SHOW INDEX est spécifique MySQL — ignoré sur sqlite (tests) :
            // l'unique simple coexiste alors avec le composite sans impact
            $compositeAlreadyExists = false;

            if (\DB::connection()->getDriverName() === 'mysql') {
                $indexes = \DB::select("SHOW INDEX FROM users WHERE Key_name = 'users_email_unique'");
                if (!empty($indexes)) {
                    Schema::table('users', function (Blueprint $table) {
                        $table->dropUnique('users_email_unique');
                    });
                }

                $compositeAlreadyExists = !empty(
                    \DB::select("SHOW INDEX FROM users WHERE Key_name = 'users_tenant_id_email_unique'")
                );
            }

            Schema::table('users', function (Blueprint $table) use ($compositeAlreadyExists) {
                $table->string('tenant_id')->nullable()->after('id');
                $table->index('tenant_id');
                if (!$compositeAlreadyExists) {
                    $table->unique(['tenant_id', 'email']);
                }
            });
        }

        // Settings: drop unique on key, replace with composite (tenant_id, key)
        if (Schema::hasTable('settings') && Schema::hasColumn('settings', 'tenant_id')) {
            if (\DB::connection()->getDriverName() === 'mysql') {
                $indexes = \DB::select("SHOW INDEX FROM settings WHERE Key_name = 'settings_key_unique'");
                if (!empty($indexes)) {
                    Schema::table('settings', function (Blueprint $table) {
                        $table->dropUnique('settings_key_unique');
                    });
                }
                $compositeExists = \DB::select("SHOW INDEX FROM settings WHERE Key_name = 'settings_tenant_id_key_unique'");
                if (empty($compositeExists)) {
                    Schema::table('settings', function (Blueprint $table) {
                        $table->unique(['tenant_id', 'key']);
                    });
                }
            } elseif (!collect(\DB::select("PRAGMA index_list('settings')"))->contains(fn ($i) => $i->name === 'settings_tenant_id_key_unique')) {
                Schema::table('settings', function (Blueprint $table) {
                    $table->unique(['tenant_id', 'key']);
                });
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'families',
            'children',
            'parents',
            'school_classes',
            'garderie_presences',
            'garderie_events',
            'cantine_presences',
            'cantine_events',
            'settings',
            'family_invitations',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'tenant_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropIndex(['tenant_id']);
                    $table->dropColumn('tenant_id');
                });
            }
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'tenant_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique(['tenant_id', 'email']);
                $table->dropIndex(['tenant_id']);
                $table->dropColumn('tenant_id');
                $table->unique('email');
            });
        }
    }
};
