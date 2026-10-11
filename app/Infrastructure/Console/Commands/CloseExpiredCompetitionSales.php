<?php

declare(strict_types=1);

namespace App\Infrastructure\Console\Commands;

use App\Application\UseCases\Competition\ChangeCompetitionStatusUseCase;
use App\Domain\Competition\Enums\CompetitionStatus;
use App\Domain\Competition\Models\Competition;
use Illuminate\Console\Command;

/**
 * Auto-closes sales once sales_close_datetime passes, per the spec:
 * "عند الوصول إلى sales_close_datetime قم بإغلاق البيع تلقائيًا."
 * Scheduled every minute (see routes/console.php).
 */
class CloseExpiredCompetitionSales extends Command
{
    protected $signature = 'competitions:close-expired-sales';

    protected $description = 'Automatically move Active competitions past their sales_close_datetime to Sales Closed';

    public function handle(ChangeCompetitionStatusUseCase $useCase): int
    {
        $expired = Competition::query()
            ->where('status', CompetitionStatus::Active->value)
            ->whereNotNull('sales_close_datetime')
            ->where('sales_close_datetime', '<=', now())
            ->get();

        foreach ($expired as $competition) {
            $useCase->execute($competition, CompetitionStatus::SalesClosed);
            $this->info("Closed sales for competition #{$competition->id}: {$competition->name}");
        }

        if ($expired->isEmpty()) {
            $this->line('No competitions to close.');
        }

        return self::SUCCESS;
    }
}
