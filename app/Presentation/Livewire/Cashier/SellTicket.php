<?php

declare(strict_types=1);

namespace App\Presentation\Livewire\Cashier;

use App\Application\DTOs\Sale\SellTicketData;
use App\Application\UseCases\Sale\SellTicketUseCase;
use App\Domain\Ticket\Enums\TicketStatus;
use App\Domain\Ticket\Models\Ticket;
use App\Domain\Ticket\Services\TicketQrCodeGenerator;
use App\Shared\Exceptions\DomainException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class SellTicket extends Component
{
    public string $ticketNumber = '';

    public string $customerName = '';

    public string $customerPhone = '';

    public ?string $successMessage = null;

    /** @var array{ticketNumber: int, competitionName: string, customerName: string, customerPhone: string, drawDate: ?string, verifyUrl: string, qrDataUri: string}|null */
    public ?array $soldTicket = null;

    public function pickRandomAvailableTicket(): void
    {
        $this->successMessage = null;
        $this->soldTicket = null;
        $this->resetErrorBag();

        $ticket = Ticket::query()
            ->where('cashier_id', auth()->id())
            ->where('status', TicketStatus::AssignedToCashier->value)
            ->inRandomOrder()
            ->first();

        if (! $ticket) {
            $this->addError('ticketNumber', __('No available tickets to pick from.'));

            return;
        }

        $this->ticketNumber = (string) $ticket->ticket_number;
    }

    public function sell(SellTicketUseCase $useCase, TicketQrCodeGenerator $qrCodeGenerator): void
    {
        $this->successMessage = null;
        $this->soldTicket = null;

        $data = $this->validate([
            'ticketNumber' => ['required', 'digits:7'],
            'customerName' => ['required', 'string', 'max:255'],
            'customerPhone' => ['required', 'string', 'max:30'],
        ]);

        $ticket = Ticket::query()->where('ticket_number', $data['ticketNumber'])->first();

        if (! $ticket) {
            $this->addError('ticketNumber', __('No ticket found with this number.'));

            return;
        }

        try {
            $useCase->execute(
                new SellTicketData($ticket->id, $data['customerName'], $data['customerPhone']),
                auth()->user(),
            );
        } catch (DomainException $e) {
            $this->addError('ticketNumber', __($e->getMessage()));

            return;
        }

        $ticket = $ticket->fresh(['competition']);

        $this->soldTicket = [
            'ticketNumber' => $ticket->ticket_number,
            'competitionName' => $ticket->competition->name,
            'customerName' => $data['customerName'],
            'customerPhone' => $data['customerPhone'],
            'drawDate' => $ticket->competition->draw_datetime?->format('Y/m/d'),
            'verifyUrl' => $qrCodeGenerator->verifyUrl($ticket),
            'qrDataUri' => $qrCodeGenerator->dataUri($ticket, 400),
        ];

        $this->successMessage = __('Ticket :number sold successfully.', ['number' => $data['ticketNumber']]);
        $this->reset(['ticketNumber', 'customerName', 'customerPhone']);
    }

    public function render()
    {
        return view('livewire.cashier.sell-ticket');
    }
}
