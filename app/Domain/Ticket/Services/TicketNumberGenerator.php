<?php

declare(strict_types=1);

namespace App\Domain\Ticket\Services;

use App\Domain\Ticket\Enums\TicketStatus;
use App\Domain\Ticket\Exceptions\TicketGenerationFailedException;
use App\Shared\ValueObjects\TicketNumber;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Efficiently batch-generates unique 7-digit ticket numbers for a competition.
 *
 * Strategy: build unique random candidates in memory (cheap — the 1,000,000–9,999,999
 * range is ~9M wide, so collisions are rare), bulk INSERT IGNORE them in chunks so a
 * collision with an existing row never fails the whole batch, then top up any shortfall
 * in further rounds. The DB unique index on ticket_number is the final safety net.
 */
class TicketNumberGenerator
{
    private const int CHUNK_SIZE = 1000;

    private const int MAX_ROUNDS = 25;

    public function generateForCompetition(int $competitionId, int $count): int
    {
        $remaining = $count;
        $created = 0;
        $now = now();

        for ($round = 0; $round < self::MAX_ROUNDS && $remaining > 0; $round++) {
            $candidates = $this->randomUniqueCandidates($remaining);

            foreach (array_chunk($candidates, self::CHUNK_SIZE) as $chunk) {
                $rows = array_map(fn (int $number) => [
                    'uuid' => (string) Str::uuid(),
                    'public_token' => (string) Str::uuid(),
                    'competition_id' => $competitionId,
                    'ticket_number' => $number,
                    'status' => TicketStatus::Generated->value,
                    'is_winner' => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ], $chunk);

                $insertedInChunk = DB::table('tickets')->insertOrIgnore($rows);

                $created += $insertedInChunk;
                $remaining -= $insertedInChunk;

                if ($remaining <= 0) {
                    break;
                }
            }
        }

        if ($remaining > 0) {
            throw TicketGenerationFailedException::unableToAllocate($remaining);
        }

        return $created;
    }

    /**
     * @return list<int>
     */
    private function randomUniqueCandidates(int $count): array
    {
        $set = [];

        while (count($set) < $count) {
            $set[TicketNumber::generateRandom()->toInt()] = true;
        }

        return array_keys($set);
    }
}
