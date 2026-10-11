<?php

declare(strict_types=1);

namespace App\Application\UseCases\Sale;

use App\Application\DTOs\Sale\SellTicketData;
use App\Domain\Audit\Services\AuditLogger;
use App\Domain\Competition\Exceptions\CompetitionClosedException;
use App\Domain\Competition\Models\Competition;
use App\Domain\Customer\Models\Customer;
use App\Domain\Sale\Models\TicketSale;
use App\Domain\Ticket\Enums\TicketStatus;
use App\Domain\Ticket\Events\TicketSold;
use App\Domain\Ticket\Exceptions\TicketAlreadySoldException;
use App\Domain\Ticket\Exceptions\TicketNotAssignedException;
use App\Domain\Ticket\Models\Ticket;
use App\Domain\User\Models\User;
use Illuminate\Support\Facades\DB;

final readonly class SellTicketUseCase
{
    public function __construct(
        private AuditLogger $auditLogger,
    ) {}

    public function execute(SellTicketData $data, User $cashier): TicketSale
    {
        return DB::transaction(function () use ($data, $cashier) {
            /** @var Ticket $ticket */
            $ticket = Ticket::query()->lockForUpdate()->findOrFail($data->ticketId);

            if ($ticket->cashier_id !== $cashier->id) {
                throw TicketNotAssignedException::toCashier($ticket->ticket_number);
            }

            if (in_array($ticket->status, [TicketStatus::Sold, TicketStatus::Winner, TicketStatus::PrizePaid], true)) {
                throw TicketAlreadySoldException::forTicketNumber($ticket->ticket_number);
            }

            if ($ticket->status !== TicketStatus::AssignedToCashier) {
                throw TicketNotAssignedException::toCashier($ticket->ticket_number);
            }

            /** @var Competition $competition */
            $competition = Competition::query()->lockForUpdate()->findOrFail($ticket->competition_id);

            if (! $competition->isSalesOpen()) {
                throw CompetitionClosedException::salesNotOpen();
            }

            $customer = Customer::query()->firstOrCreate(
                [
                    'cashier_id' => $cashier->id,
                    'phone' => $data->customerPhone,
                ],
                [
                    'name' => $data->customerName,
                    'master_id' => $cashier->master_id,
                ]
            );

            $ticketPrice = $competition->ticket_price;
            $cashierCommission = $competition->cashier_commission_fixed;
            $masterCommission = $competition->master_commission_fixed;
            $netAmount = $ticketPrice - $cashierCommission - $masterCommission;
            $now = now();

            $sale = TicketSale::query()->create([
                'competition_id' => $competition->id,
                'ticket_id' => $ticket->id,
                'customer_id' => $customer->id,
                'master_id' => $cashier->master_id,
                'cashier_id' => $cashier->id,
                'ticket_price' => $ticketPrice,
                'cashier_commission' => $cashierCommission,
                'master_commission' => $masterCommission,
                'gross_amount' => $ticketPrice,
                'net_amount' => $netAmount,
                'status' => 'completed',
                'sold_at' => $now,
            ]);

            $ticket->update([
                'customer_id' => $customer->id,
                'status' => TicketStatus::Sold,
                'price_at_sale' => $ticketPrice,
                'cashier_commission_at_sale' => $cashierCommission,
                'master_commission_at_sale' => $masterCommission,
                'sold_at' => $now,
            ]);

            $this->auditLogger->log('Ticket Sold', $ticket, [], [
                'ticket_number' => $ticket->ticket_number,
                'customer_id' => $customer->id,
                'price' => $ticketPrice,
            ]);

            event(new TicketSold($ticket->fresh()));

            return $sale;
        });
    }
}
