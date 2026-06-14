<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('amount');
            $table->enum('method', ['bkash', 'nagad']);
            $table->string('trx_id');
            $table->string('screenshot_path')->nullable();
            $table->enum('status', ['submitted', 'confirmed', 'rejected'])->default('submitted');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
