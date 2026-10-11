<?php

declare(strict_types=1);

namespace App\Application\Queries\Dashboard;

use App\Domain\Sale\Models\TicketSale;
use App\Domain\Ticket\Enums\TicketStatus;
use Illuminate\Support\Facades\DB;

final readonly class GetCashierDashboardStatsQuery
{
    /**
     * @return array{stats: array<string, int>, recent_sales: array<int, TicketSale>}
     */
    public function execute(int $cashierId): array
    {
        $soldStatuses = [TicketStatus::Sold->value, TicketStatus::Winner->value, TicketStatus::PrizePaid->value];

        $statuses = DB::table('tickets')
            ->where('cashier_id', $cashierId)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $assigned = (int) $statuses->sum();
        $sold = (int) collect($soldStatuses)->sum(fn ($s) => $statuses[$s] ?? 0);
        $cancelled = (int) ($statuses[TicketStatus::Cancelled->value] ?? 0);
        $remaining = max($assigned - $sold - $cancelled, 0);

        $sales = DB::table('ticket_sales')
            ->where('cashier_id', $cashierId)
            ->where('status', 'completed')
            ->selectRaw('COALESCE(SUM(gross_amount), 0) as sales')
            ->selectRaw('COALESCE(SUM(cashier_commission), 0) as commission')
            ->first();

        $recentSales = TicketSale::query()
            ->where('cashier_id', $cashierId)
            ->with(['ticket', 'customer'])
            ->latest('sold_at')
            ->limit(10)
            ->get();

        return [
            'stats' => [
                'assigned_tickets' => $assigned,
                'sold_tickets' => $sold,
                'remaining_tickets' => $remaining,
                'virtual_sales' => (int) $sales->sales,
                'virtual_commission' => (int) $sales->commission,
            ],
            'recent_sales' => $recentSales->all(),
        ];
    }
}
