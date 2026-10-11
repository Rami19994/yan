<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Api\V1\Cashier;

use App\Application\DTOs\Sale\SellTicketData;
use App\Application\UseCases\Sale\SellTicketUseCase;
use App\Domain\Ticket\Models\Ticket;
use App\Presentation\Http\Controllers\Controller;
use App\Presentation\Http\Requests\Api\V1\Cashier\SellTicketRequest;
use App\Presentation\Http\Resources\TicketSaleResource;
use App\Shared\Exceptions\DomainException;
use Illuminate\Http\JsonResponse;

class SellTicketController extends Controller
{
    public function __invoke(SellTicketRequest $request, Ticket $ticket, SellTicketUseCase $useCase): JsonResponse
    {
        $data = $request->validated();

        try {
            $sale = $useCase->execute(
                new SellTicketData($ticket->id, $data['customer_name'], $data['customer_phone']),
                $request->user(),
            );
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return (new TicketSaleResource($sale))
            ->response()
            ->setStatusCode(201);
    }
}
