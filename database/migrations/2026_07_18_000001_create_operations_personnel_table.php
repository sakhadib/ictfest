<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operations_personnel', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('student_id')->nullable()->index();
            $table->string('phone')->nullable()->index();
            $table->string('team')->nullable()->index();
            $table->string('status', 30)->default('other')->index();
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operations_personnel');
    }
};
