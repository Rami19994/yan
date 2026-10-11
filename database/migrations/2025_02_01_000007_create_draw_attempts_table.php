<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('draw_attempts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('draw_id')->nullable()->constrained('draws')->cascadeOnDelete();
            $table->foreignId('competition_id')->constrained('competitions')->cascadeOnDelete();

            $table->unsignedInteger('attempt_number')->default(1);

            $table->string('draw_mode');
            $table->unsignedInteger('result_number');

            $table->foreignId('selected_ticket_id')->nullable()->constrained('tickets')->nullOnDelete();

            $table->boolean('is_winner')->default(false);
            $table->foreignId('winner_ticket_id')->nullable()->constrained('tickets')->nullOnDelete();

            $table->foreignId('started_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();

            // SHA-256 integrity hash of competition_id + attempt + result + timestamp.
            $table->string('hash', 64);

            $table->timestamp('created_at')->useCurrent();

            $table->index(['competition_id', 'attempt_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('draw_attempts');
    }
};
