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
        Schema::create('repeated_students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->enum('track_start', ['html', 'css', 'javascript', 'php', 'mysql', 'project'])->nullable();
            $table->enum('repeat_status', ['waiting', 'accepted'])->default('waiting');
            $table->foreignIdFor(\App\Models\Group::class)->nullable();
            $table->foreignIdFor(\App\Models\Instructor::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(\App\Models\Branch::class)->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repeated_students');
    }
};
