<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('iupc_bkash_recipients', function (Blueprint $table) {
            $table->id();
            $table->string('recipient_name');
            $table->string('bkash_number', 20);
            $table->boolean('is_enabled')->default(true);
            $table->boolean('is_current')->default(false);
            $table->string('current_lock')->nullable()->unique();
            $table->unsignedInteger('rotation_order')->default(0);
            $table->timestamp('deactivated_at')->nullable();
            $table->timestamp('reactivate_at')->nullable();
            $table->timestamp('last_selected_at')->nullable();
            $table->timestamps();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('iupc_bkash_recipient_id')->nullable()->after('method')->constrained('iupc_bkash_recipients')->nullOnDelete();
            $table->string('recipient_name')->nullable()->after('iupc_bkash_recipient_id');
            $table->string('recipient_number', 20)->nullable()->after('recipient_name');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('iupc_bkash_recipient_id');
            $table->dropColumn(['recipient_name', 'recipient_number']);
        });

        Schema::dropIfExists('iupc_bkash_recipients');
    }
};
