<?php

declare(strict_types=1);

namespace App\Application\Queries\Dashboard;

use App\Domain\Competition\Models\Competition;
use App\Domain\Ticket\Enums\TicketStatus;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Models\User;
use Illuminate\Support\Facades\DB;

final readonly class GetAdminDashboardStatsQuery
{
    /**
     * @return array<string, int>
     */
    public function execute(): array
    {
        $ticketStatusCounts = DB::table('tickets')
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $sold = (int) ($ticketStatusCounts[TicketStatus::Sold->value] ?? 0)
            + (int) ($ticketStatusCounts[TicketStatus::Winner->value] ?? 0)
            + (int) ($ticketStatusCounts[TicketStatus::PrizePaid->value] ?? 0);

        $cancelled = (int) ($ticketStatusCounts[TicketStatus::Cancelled->value] ?? 0);
        $totalTickets = (int) $ticketStatusCounts->sum();
        $available = $totalTickets - $sold - $cancelled;

        $sales = DB::table('ticket_sales')
            ->where('status', 'completed')
            ->selectRaw('COALESCE(SUM(gross_amount), 0) as total_sales')
            ->selectRaw('COALESCE(SUM(cashier_commission), 0) as cashier_commission')
            ->selectRaw('COALESCE(SUM(master_commission), 0) as master_commission')
            ->selectRaw('COALESCE(SUM(net_amount), 0) as net_revenue')
            ->first();

        return [
            'total_masters' => User::query()->role(UserRole::Master->value)->count(),
            'total_cashiers' => User::query()->role(UserRole::Cashier->value)->count(),
            'total_competitions' => Competition::query()->count(),
            'total_tickets' => $totalTickets,
            'sold_tickets' => $sold,
            'available_tickets' => max($available, 0),
            'cancelled_tickets' => $cancelled,
            'total_virtual_sales' => (int) $sales->total_sales,
            'cashier_virtual_commission' => (int) $sales->cashier_commission,
            'master_virtual_commission' => (int) $sales->master_commission,
            'virtual_net_revenue' => (int) $sales->net_revenue,
            'virtual_prize_amount' => (int) Competition::query()->sum('prize_amount'),
            'winners_count' => DB::table('winners')->count(),
        ];
    }
}
