<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('session_id')->nullable();

            $table->timestamp('login_at')->nullable();
            $table->timestamp('logout_at')->nullable();

            $table->string('ended_by')->nullable();

            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            $table->index('session_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_logs');
    }
};
