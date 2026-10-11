<?php

declare(strict_types=1);

namespace App\Application\UseCases\Ticket;

use App\Application\DTOs\Ticket\GenerateTicketsData;
use App\Domain\Audit\Services\AuditLogger;
use App\Domain\Competition\Models\Competition;
use App\Domain\Ticket\Exceptions\TicketGenerationNotAllowedException;
use App\Domain\Ticket\Services\TicketNumberGenerator;
use App\Domain\User\Models\User;

final readonly class GenerateTicketsUseCase
{
    public function __construct(
        private TicketNumberGenerator $generator,
        private AuditLogger $auditLogger,
    ) {}

    public function execute(GenerateTicketsData $data, User $admin): int
    {
        $competition = Competition::query()->findOrFail($data->competitionId);

        if ($competition->status->isTerminal()) {
            throw TicketGenerationNotAllowedException::competitionIsTerminal();
        }

        $created = $this->generator->generateForCompetition($competition->id, $data->count);

        $this->auditLogger->log(
            'Tickets Generated',
            $competition,
            [],
            ['requested' => $data->count, 'created' => $created],
            $admin->id,
        );

        return $created;
    }
}
