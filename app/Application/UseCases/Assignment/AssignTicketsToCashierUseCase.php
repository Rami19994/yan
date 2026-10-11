<?php

declare(strict_types=1);

namespace App\Application\UseCases\Assignment;

use App\Application\DTOs\Assignment\AssignTicketsToCashierData;
use App\Domain\Assignment\Enums\AssignmentType;
use App\Domain\Assignment\Events\TicketsAssigned;
use App\Domain\Assignment\Exceptions\CashierNotOwnedByMasterException;
use App\Domain\Assignment\Exceptions\NotEnoughTicketsAvailableException;
use App\Domain\Audit\Services\AuditLogger;
use App\Domain\Ticket\Enums\TicketStatus;
use App\Domain\Ticket\Models\Ticket;
use App\Domain\User\Models\User;
use Illuminate\Support\Facades\DB;

final readonly class AssignTicketsToCashierUseCase
{
    public function __construct(
        private AuditLogger $auditLogger,
    ) {}

    public function execute(AssignTicketsToCashierData $data, User $master): int
    {
        return DB::transaction(function () use ($data, $master) {
            $cashier = User::query()->findOrFail($data->cashierId);

            if ($cashier->master_id !== $master->id) {
                throw CashierNotOwnedByMasterException::create();
            }

            $ids = Ticket::query()
                ->where('competition_id', $data->competitionId)
                ->where('master_id', $master->id)
                ->where('status', TicketStatus::AssignedToMaster->value)
                ->orderBy('id')
                ->limit($data->count)
                ->lockForUpdate()
                ->pluck('id');

            if ($ids->count() < $data->count) {
                throw NotEnoughTicketsAvailableException::create($data->count, $ids->count());
            }

            $now = now();

            foreach ($ids->chunk(1000) as $idChunk) {
                Ticket::query()->whereIn('id', $idChunk)->update([
                    'cashier_id' => $data->cashierId,
                    'status' => TicketStatus::AssignedToCashier->value,
                    'assigned_to_cashier_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $assignmentRows = $ids->map(fn (int $ticketId) => [
                'ticket_id' => $ticketId,
                'from_user_id' => $master->id,
                'to_user_id' => $data->cashierId,
                'assignment_type' => AssignmentType::MasterToCashier->value,
                'assigned_by' => $master->id,
                'created_at' => $now,
            ])->all();

            foreach (array_chunk($assignmentRows, 1000) as $chunk) {
                DB::table('ticket_assignments')->insert($chunk);
            }

            $this->auditLogger->log('Tickets Assigned', null, [], [
                'competition_id' => $data->competitionId,
                'cashier_id' => $data->cashierId,
                'count' => $ids->count(),
            ]);

            event(new TicketsAssigned($data->cashierId, $data->competitionId, $ids->count(), $master));

            return $ids->count();
        });
    }
}
