<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Consolidated migration: creates all tenant tables in the central database
 * with tenant_id column for single-database multi-tenancy.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Families
        if (!Schema::hasTable('families')) {
            Schema::create('families', function (Blueprint $table) {
                $table->id();
                $table->string('tenant_id');
                $table->string('family_name');
                $table->string('address')->nullable();
                $table->string('postal_code', 10)->nullable();
                $table->string('city')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->text('notes')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->index('tenant_id');
            });
        }

        // Parents
        if (!Schema::hasTable('parents')) {
            Schema::create('parents', function (Blueprint $table) {
                $table->id();
                $table->string('tenant_id');
                $table->foreignId('family_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('first_name');
                $table->string('last_name');
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('mobile')->nullable();
                $table->enum('relationship', ['mother', 'father', 'guardian', 'other'])->default('other');
                $table->boolean('is_primary_contact')->default(false);
                $table->boolean('can_pickup')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->index('tenant_id');
            });
        }

        // Children
        if (!Schema::hasTable('children')) {
            Schema::create('children', function (Blueprint $table) {
                $table->id();
                $table->string('tenant_id');
                $table->foreignId('family_id')->nullable()->constrained()->onDelete('set null');
                $table->string('first_name');
                $table->string('last_name');
                $table->date('birth_date')->nullable();
                $table->enum('gender', ['M', 'F'])->nullable();
                $table->string('class')->nullable();
                $table->foreignId('class_id')->nullable()->constrained('school_classes')->onDelete('set null');
                $table->text('allergies')->nullable();
                $table->text('medical_notes')->nullable();
                $table->text('dietary_restrictions')->nullable();
                $table->string('photo_path')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('garderie_subscribed')->default(false);
                $table->boolean('cantine_subscribed')->default(false);
                $table->timestamps();
                $table->softDeletes();

                $table->index('tenant_id');
                $table->index('family_id');
            });
        }

        // School classes
        if (!Schema::hasTable('school_classes')) {
            Schema::create('school_classes', function (Blueprint $table) {
                $table->id();
                $table->string('tenant_id');
                $table->string('name');
                $table->string('teacher_name')->nullable();
                $table->string('school_year');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->index(['tenant_id', 'school_year', 'is_active']);
            });
        }

        // Garderie presences
        if (!Schema::hasTable('garderie_presences')) {
            Schema::create('garderie_presences', function (Blueprint $table) {
                $table->id();
                $table->string('tenant_id');
                $table->foreignId('child_id')->constrained()->onDelete('cascade');
                $table->date('date');
                $table->time('arrival_time')->nullable();
                $table->time('departure_time')->nullable();
                $table->foreignId('recorded_by_arrival')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('recorded_by_departure')->nullable()->constrained('users')->onDelete('set null');
                $table->integer('duration_minutes')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index('tenant_id');
                $table->index(['child_id', 'date']);
                $table->index('date');
            });
        }

        // Garderie events
        if (!Schema::hasTable('garderie_events')) {
            Schema::create('garderie_events', function (Blueprint $table) {
                $table->id();
                $table->string('tenant_id');
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

                $table->index('tenant_id');
                $table->index(['child_id', 'event_date']);
            });
        }

        // Cantine presences
        if (!Schema::hasTable('cantine_presences')) {
            Schema::create('cantine_presences', function (Blueprint $table) {
                $table->id();
                $table->string('tenant_id');
                $table->foreignId('child_id')->constrained()->onDelete('cascade');
                $table->date('date');
                $table->enum('meal_type', ['lunch', 'snack'])->default('lunch');
                $table->boolean('is_present')->default(true);
                $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index('tenant_id');
                $table->unique(['child_id', 'date', 'meal_type']);
                $table->index(['date', 'meal_type']);
            });
        }

        // Cantine events
        if (!Schema::hasTable('cantine_events')) {
            Schema::create('cantine_events', function (Blueprint $table) {
                $table->id();
                $table->string('tenant_id');
                $table->foreignId('child_id')->constrained()->onDelete('cascade');
                $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
                $table->date('event_date');
                $table->time('event_time')->nullable();
                $table->enum('event_type', ['allergy', 'refusal', 'incident', 'other'])->default('other');
                $table->string('title');
                $table->text('description');
                $table->boolean('parents_notified')->default(false);
                $table->timestamp('notified_at')->nullable();
                $table->timestamps();

                $table->index('tenant_id');
                $table->index(['child_id', 'event_date']);
            });
        }

        // Settings
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('tenant_id');
                $table->string('key');
                $table->text('value')->nullable();
                $table->string('type')->default('string');
                $table->string('group')->default('general');
                $table->text('description')->nullable();
                $table->timestamps();

                $table->index('tenant_id');
                $table->unique(['tenant_id', 'key']);
            });
        }

        // Family invitations
        if (!Schema::hasTable('family_invitations')) {
            Schema::create('family_invitations', function (Blueprint $table) {
                $table->id();
                $table->string('tenant_id');
                $table->foreignId('family_id')->constrained()->onDelete('cascade');
                $table->string('email');
                $table->string('token')->unique();
                $table->enum('status', ['pending', 'accepted', 'expired'])->default('pending');
                $table->datetime('expires_at')->nullable();
                $table->datetime('accepted_at')->nullable();
                $table->timestamps();

                $table->index('tenant_id');
                $table->index(['family_id', 'status']);
            });
        }

        // Add tenant_id to users table
        if (!Schema::hasColumn('users', 'tenant_id')) {
            // Drop unique on email if it exists, replace with composite unique (tenant_id, email)
            $indexes = \DB::select("SHOW INDEX FROM users WHERE Key_name = 'users_email_unique'");
            if (!empty($indexes)) {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropUnique('users_email_unique');
                });
            }

            Schema::table('users', function (Blueprint $table) {
                $table->string('tenant_id')->nullable()->after('id');
                $table->index('tenant_id');
                $table->unique(['tenant_id', 'email']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('family_invitations');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('cantine_events');
        Schema::dropIfExists('cantine_presences');
        Schema::dropIfExists('garderie_events');
        Schema::dropIfExists('garderie_presences');
        Schema::dropIfExists('school_classes');
        Schema::dropIfExists('children');
        Schema::dropIfExists('parents');
        Schema::dropIfExists('families');

        if (Schema::hasColumn('users', 'tenant_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex(['tenant_id']);
                $table->dropColumn('tenant_id');
            });
        }
    }
};
