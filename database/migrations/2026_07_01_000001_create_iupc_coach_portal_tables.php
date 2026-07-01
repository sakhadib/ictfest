<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('iupc_university_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->unsignedInteger('slot_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('iupc_university_aliases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('iupc_university_allocation_id')->constrained()->cascadeOnDelete();
            $table->string('raw_name');
            $table->string('normalized_name')->unique();
            $table->unsignedInteger('source_count')->default(0);
            $table->timestamps();
        });

        Schema::create('iupc_coach_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('iupc_university_allocation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('registration_coach_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('designation')->nullable();
            $table->string('official_email');
            $table->string('normalized_email');
            $table->string('contact_number')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['iupc_university_allocation_id', 'normalized_email'], 'iupc_coaches_allocation_email_unique');
        });

        Schema::create('iupc_coach_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('iupc_university_allocation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('iupc_coach_contact_id')->constrained()->cascadeOnDelete();
            $table->string('token_hash', 64)->unique();
            $table->text('token_encrypted');
            $table->timestamp('last_sent_at')->nullable();
            $table->timestamp('last_email_sent_at')->nullable();
            $table->timestamp('last_sms_sent_at')->nullable();
            $table->timestamp('disabled_at')->nullable();
            $table->foreignId('disabled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('iupc_coach_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('iupc_university_allocation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('iupc_coach_link_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('registration_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->text('summary');
            $table->json('before')->nullable();
            $table->json('after')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });

        Schema::table('final_registrations', function (Blueprint $table) {
            $table->string('payment_package', 40)->nullable()->after('status');
            $table->unsignedInteger('payment_amount')->nullable()->after('payment_package');
        });
    }

    public function down(): void
    {
        Schema::table('final_registrations', function (Blueprint $table) {
            $table->dropColumn(['payment_package', 'payment_amount']);
        });

        Schema::dropIfExists('iupc_coach_activity_logs');
        Schema::dropIfExists('iupc_coach_links');
        Schema::dropIfExists('iupc_coach_contacts');
        Schema::dropIfExists('iupc_university_aliases');
        Schema::dropIfExists('iupc_university_allocations');
    }
};
