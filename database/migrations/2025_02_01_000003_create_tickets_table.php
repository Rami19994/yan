<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // Secure public token used in QR codes / verification URLs — never the raw ticket number.
            $table->uuid('public_token')->unique();

            $table->foreignId('competition_id')->constrained('competitions')->cascadeOnDelete();

            // 7-digit ticket number (1,000,000–9,999,999), unique system-wide.
            $table->unsignedInteger('ticket_number')->unique();

            $table->foreignId('master_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cashier_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();

            $table->string('status')->default('generated')->index();

            $table->unsignedBigInteger('price_at_sale')->nullable();
            $table->unsignedBigInteger('cashier_commission_at_sale')->nullable();
            $table->unsignedBigInteger('master_commission_at_sale')->nullable();

            $table->timestamp('assigned_to_master_at')->nullable();
            $table->timestamp('assigned_to_cashier_at')->nullable();
            $table->timestamp('sold_at')->nullable();

            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('cancellation_reason')->nullable();

            $table->boolean('is_winner')->default(false);

            $table->timestamps();

            $table->index(['competition_id', 'status']);
            $table->index(['master_id', 'status']);
            $table->index(['cashier_id', 'status']);
        });

        // Attach the FK from competitions.selected_winner_ticket_id now that tickets exists.
        Schema::table('competitions', function (Blueprint $table) {
            $table->foreign('selected_winner_ticket_id')->references('id')->on('tickets')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropForeign(['selected_winner_ticket_id']);
        });

        Schema::dropIfExists('tickets');
    }
};
