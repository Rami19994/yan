<?php

declare(strict_types=1);

namespace App\Presentation\Livewire\Admin;

use App\Application\DTOs\Competition\CompetitionData;
use App\Application\UseCases\Competition\CreateCompetitionUseCase;
use App\Application\UseCases\Competition\UpdateCompetitionUseCase;
use App\Domain\Competition\Enums\DrawMode;
use App\Domain\Competition\Enums\NoWinnerPolicy;
use App\Domain\Competition\Models\Competition;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class CompetitionForm extends Component
{
    public ?int $competitionId = null;

    public string $name = '';

    public string $description = '';

    public string $ticketPrice = '';

    public string $cashierCommissionFixed = '0';

    public string $masterCommissionFixed = '0';

    public string $prizeAmount = '0';

    public string $drawMode = 'random';

    public string $noWinnerPolicy = 'no_winner';

    public string $drawDatetime = '';

    public string $salesOpenDatetime = '';

    public string $salesCloseDatetime = '';

    public function mount(?Competition $competition = null): void
    {
        if ($competition && $competition->exists) {
            Gate::authorize('update', $competition);

            $this->competitionId = $competition->id;
            $this->name = $competition->name;
            $this->description = (string) $competition->description;
            $this->ticketPrice = (string) $competition->ticket_price;
            $this->cashierCommissionFixed = (string) $competition->cashier_commission_fixed;
            $this->masterCommissionFixed = (string) $competition->master_commission_fixed;
            $this->prizeAmount = (string) $competition->prize_amount;
            $this->drawMode = $competition->draw_mode->value;
            $this->noWinnerPolicy = $competition->no_winner_policy->value;
            $this->drawDatetime = $competition->draw_datetime?->format('Y-m-d\TH:i') ?? '';
            $this->salesOpenDatetime = $competition->sales_open_datetime?->format('Y-m-d\TH:i') ?? '';
            $this->salesCloseDatetime = $competition->sales_close_datetime?->format('Y-m-d\TH:i') ?? '';
        } else {
            Gate::authorize('create', Competition::class);
        }
    }

    public function save(CreateCompetitionUseCase $createUseCase, UpdateCompetitionUseCase $updateUseCase): void
    {
        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'ticketPrice' => ['required', 'integer', 'min:0'],
            'cashierCommissionFixed' => ['required', 'integer', 'min:0'],
            'masterCommissionFixed' => ['required', 'integer', 'min:0'],
            'prizeAmount' => ['required', 'integer', 'min:0'],
            'drawMode' => ['required', 'in:random,admin_selected'],
            'noWinnerPolicy' => ['required', 'in:no_winner,redraw'],
            'drawDatetime' => ['nullable', 'date'],
            'salesOpenDatetime' => ['nullable', 'date'],
            'salesCloseDatetime' => ['nullable', 'date', 'after:salesOpenDatetime'],
        ]);

        $dto = new CompetitionData(
            name: $data['name'],
            description: $data['description'] ?: null,
            ticketPrice: (int) $data['ticketPrice'],
            cashierCommissionFixed: (int) $data['cashierCommissionFixed'],
            masterCommissionFixed: (int) $data['masterCommissionFixed'],
            prizeAmount: (int) $data['prizeAmount'],
            drawMode: DrawMode::from($data['drawMode']),
            noWinnerPolicy: NoWinnerPolicy::from($data['noWinnerPolicy']),
            drawDatetime: $data['drawDatetime'] ?: null,
            salesOpenDatetime: $data['salesOpenDatetime'] ?: null,
            salesCloseDatetime: $data['salesCloseDatetime'] ?: null,
        );

        if ($this->competitionId) {
            $competition = Competition::query()->findOrFail($this->competitionId);
            $updateUseCase->execute($competition, $dto);
        } else {
            $createUseCase->execute($dto, auth()->user());
        }

        $this->redirectRoute('admin.competitions.index', navigate: false);
    }

    public function render()
    {
        return view('livewire.admin.competition-form');
    }
}
