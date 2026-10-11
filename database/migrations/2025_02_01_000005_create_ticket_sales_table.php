<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_sales', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->foreignId('competition_id')->constrained('competitions')->cascadeOnDelete();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();

            $table->foreignId('master_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cashier_id')->constrained('users')->cascadeOnDelete();

            $table->unsignedBigInteger('ticket_price');
            $table->unsignedBigInteger('cashier_commission');
            $table->unsignedBigInteger('master_commission');

            $table->unsignedBigInteger('gross_amount');
            $table->unsignedBigInteger('net_amount');

            $table->string('status')->default('completed')->index();

            $table->timestamp('sold_at');

            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('cancellation_reason')->nullable();

            $table->timestamps();

            $table->index(['cashier_id', 'sold_at']);
            $table->index(['master_id', 'sold_at']);
            $table->index(['competition_id', 'sold_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_sales');
    }
};
