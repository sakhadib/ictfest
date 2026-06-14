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
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->string('registration_code')->unique()->index();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('team_name');
            $table->string('institution');
            $table->string('contact_name');
            $table->string('contact_email');
            $table->string('contact_phone');
            $table->enum('status', ['pending', 'verified', 'paid', 'rejected'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'submitted', 'confirmed'])->default('unpaid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
