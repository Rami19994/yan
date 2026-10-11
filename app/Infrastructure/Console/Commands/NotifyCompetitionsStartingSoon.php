<?php

declare(strict_types=1);

namespace App\Infrastructure\Console\Commands;

use App\Domain\Competition\Events\CompetitionStartingSoon;
use App\Domain\Competition\Models\Competition;
use Illuminate\Console\Command;

/**
 * Notifies admins once a competition's sales_open_datetime is within the
 * next hour. starting_soon_notified_at guards against re-notifying every
 * minute for the same competition. Scheduled every minute (see routes/console.php).
 */
class NotifyCompetitionsStartingSoon extends Command
{
    private const int LOOKAHEAD_MINUTES = 60;

    protected $signature = 'competitions:notify-starting-soon';

    protected $description = 'Notify admins of competitions whose sales open within the next hour';

    public function handle(): int
    {
        $upcoming = Competition::query()
            ->whereNull('starting_soon_notified_at')
            ->whereNotNull('sales_open_datetime')
            ->where('sales_open_datetime', '>', now())
            ->where('sales_open_datetime', '<=', now()->addMinutes(self::LOOKAHEAD_MINUTES))
            ->get();

        foreach ($upcoming as $competition) {
            event(new CompetitionStartingSoon($competition));

            $competition->update(['starting_soon_notified_at' => now()]);

            $this->info("Notified: competition #{$competition->id} ({$competition->name})");
        }

        if ($upcoming->isEmpty()) {
            $this->line('No competitions starting soon.');
        }

        return self::SUCCESS;
    }
}
