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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('phone')->nullable();
            $table->enum('status', ['normal', 'important'])->default('normal');
            $table->enum('start', ['directly', 'delay'])->default('delay');
            $table->foreignIdFor(\App\Models\Group::class)->nullable();
            $table->foreignIdFor(\App\Models\Branch::class)->nullable();
            $table->foreignIdFor(\App\Models\TrainingGroup::class)->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
