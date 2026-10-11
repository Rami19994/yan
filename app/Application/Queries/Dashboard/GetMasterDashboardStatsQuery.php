<?php

declare(strict_types=1);

namespace App\Application\Queries\Dashboard;

use App\Domain\Ticket\Enums\TicketStatus;
use App\Domain\User\Models\User;
use Illuminate\Support\Facades\DB;

final readonly class GetMasterDashboardStatsQuery
{
    /**
     * @return array{cashiers: array<int, array<string, mixed>>, totals: array<string, int>}
     */
    public function execute(int $masterId): array
    {
        $soldStatuses = [TicketStatus::Sold->value, TicketStatus::Winner->value, TicketStatus::PrizePaid->value];

        $ticketCounts = DB::table('tickets')
            ->where('master_id', $masterId)
            ->whereNotNull('cashier_id')
            ->select('cashier_id', 'status', DB::raw('COUNT(*) as total'))
            ->groupBy('cashier_id', 'status')
            ->get()
            ->groupBy('cashier_id');

        $salesByCashier = DB::table('ticket_sales')
            ->where('master_id', $masterId)
            ->where('status', 'completed')
            ->select('cashier_id')
            ->selectRaw('COALESCE(SUM(gross_amount), 0) as sales')
            ->selectRaw('COALESCE(SUM(cashier_commission), 0) as commission')
            ->groupBy('cashier_id')
            ->get()
            ->keyBy('cashier_id');

        $cashiers = User::query()
            ->where('master_id', $masterId)
            ->orderBy('name')
            ->get();

        $rows = $cashiers->map(function (User $cashier) use ($ticketCounts, $salesByCashier, $soldStatuses) {
            $statuses = $ticketCounts->get($cashier->id, collect())->pluck('total', 'status');

            $assigned = (int) $statuses->sum();
            $sold = (int) collect($soldStatuses)->sum(fn ($s) => $statuses[$s] ?? 0);
            $cancelled = (int) ($statuses[TicketStatus::Cancelled->value] ?? 0);
            $remaining = max($assigned - $sold - $cancelled, 0);

            $sales = $salesByCashier->get($cashier->id);

            return [
                'id' => $cashier->id,
                'name' => $cashier->name,
                'assigned' => $assigned,
                'sold' => $sold,
                'remaining' => $remaining,
                'sales' => (int) ($sales->sales ?? 0),
                'commission' => (int) ($sales->commission ?? 0),
            ];
        })->all();

        $totals = [
            'cashiers_count' => $cashiers->count(),
            'assigned_tickets' => array_sum(array_column($rows, 'assigned')),
            'sold_tickets' => array_sum(array_column($rows, 'sold')),
            'remaining_tickets' => array_sum(array_column($rows, 'remaining')),
            'virtual_sales' => array_sum(array_column($rows, 'sales')),
            'virtual_commission' => (int) DB::table('ticket_sales')
                ->where('master_id', $masterId)
                ->where('status', 'completed')
                ->sum('master_commission'),
            // Tickets the admin gave this master that haven't been handed to a
            // cashier yet — the master's own ticket stock still in hand.
            'own_ticket_balance' => (int) DB::table('tickets')
                ->where('master_id', $masterId)
                ->whereNull('cashier_id')
                ->where('status', TicketStatus::AssignedToMaster->value)
                ->count(),
        ];

        return ['cashiers' => $rows, 'totals' => $totals];
    }
}
