<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competitions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('status')->default('draft')->index();

            // All monetary values are virtual simulation amounts stored as whole IQD (bigint, no floats).
            $table->unsignedBigInteger('ticket_price');
            $table->unsignedBigInteger('cashier_commission_fixed')->default(0);
            $table->unsignedBigInteger('master_commission_fixed')->default(0);
            $table->unsignedBigInteger('prize_amount')->default(0);

            $table->string('draw_mode')->default('random');
            $table->string('no_winner_policy')->default('no_winner');

            $table->dateTime('draw_datetime')->nullable();
            $table->dateTime('sales_open_datetime')->nullable();
            $table->dateTime('sales_close_datetime')->nullable();

            // Added as a plain column here; the FK to tickets.id is attached once the
            // tickets table exists (see create_tickets_table migration).
            $table->unsignedBigInteger('selected_winner_ticket_id')->nullable();
            $table->foreignId('winner_selected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('winner_selected_at')->nullable();

            $table->timestamp('draw_started_at')->nullable();
            $table->timestamp('draw_completed_at')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'draw_datetime']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competitions');
    }
};
