<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('support_tickets')) {
            Schema::create('support_tickets', function (Blueprint $table) {
                $table->id();
                $table->string('tenant_id')->nullable();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('user_name');
                $table->string('subject');
                $table->text('description');
                $table->string('status')->default('open'); // open, in_progress, resolved, closed
                $table->string('priority')->default('normal'); // low, normal, high, urgent
                $table->string('category')->default('general'); // general, technical, billing, feature_request, bug
                $table->timestamp('resolved_at')->nullable();
                $table->timestamps();

                $table->index('tenant_id');
                $table->index('user_id');
                $table->index('status');
                $table->index('created_at');
            });
        }

        if (!Schema::hasTable('support_ticket_comments')) {
            Schema::create('support_ticket_comments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('ticket_id')->constrained('support_tickets')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('user_name');
                $table->boolean('is_staff')->default(false);
                $table->text('content');
                $table->timestamps();

                $table->index('ticket_id');
                $table->index('user_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('support_ticket_comments');
        Schema::dropIfExists('support_tickets');
    }
};
