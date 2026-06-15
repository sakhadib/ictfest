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
        Schema::create('final_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('trx_id');
            $table->timestamps();
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->string('tshirt_size', 10)->nullable()->after('is_leader');
        });

        Schema::table('registration_coaches', function (Blueprint $table) {
            $table->string('tshirt_size', 10)->nullable()->after('contact_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registration_coaches', function (Blueprint $table) {
            $table->dropColumn('tshirt_size');
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn('tshirt_size');
        });

        Schema::dropIfExists('final_registrations');
    }
};
