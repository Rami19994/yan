<?php

declare(strict_types=1);

namespace App\Application\UseCases\Assignment;

use App\Application\DTOs\Assignment\AssignTicketsToMasterData;
use App\Domain\Assignment\Enums\AssignmentType;
use App\Domain\Assignment\Events\TicketsAssigned;
use App\Domain\Assignment\Exceptions\NotEnoughTicketsAvailableException;
use App\Domain\Audit\Services\AuditLogger;
use App\Domain\Ticket\Enums\TicketStatus;
use App\Domain\Ticket\Models\Ticket;
use App\Domain\User\Models\User;
use Illuminate\Support\Facades\DB;

final readonly class AssignTicketsToMasterUseCase
{
    public function __construct(
        private AuditLogger $auditLogger,
    ) {}

    public function execute(AssignTicketsToMasterData $data, User $admin): int
    {
        return DB::transaction(function () use ($data, $admin) {
            $ids = Ticket::query()
                ->where('competition_id', $data->competitionId)
                ->where('status', TicketStatus::Generated->value)
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
                    'master_id' => $data->masterId,
                    'status' => TicketStatus::AssignedToMaster->value,
                    'assigned_to_master_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $assignmentRows = $ids->map(fn (int $ticketId) => [
                'ticket_id' => $ticketId,
                'from_user_id' => null,
                'to_user_id' => $data->masterId,
                'assignment_type' => AssignmentType::AdminToMaster->value,
                'assigned_by' => $admin->id,
                'created_at' => $now,
            ])->all();

            foreach (array_chunk($assignmentRows, 1000) as $chunk) {
                DB::table('ticket_assignments')->insert($chunk);
            }

            $this->auditLogger->log('Tickets Assigned', null, [], [
                'competition_id' => $data->competitionId,
                'master_id' => $data->masterId,
                'count' => $ids->count(),
            ]);

            event(new TicketsAssigned($data->masterId, $data->competitionId, $ids->count(), $admin));

            return $ids->count();
        });
    }
}
