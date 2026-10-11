<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Api\V1\Cashier;

use App\Domain\Sale\Models\TicketSale;
use App\Presentation\Http\Controllers\Controller;
use App\Presentation\Http\Resources\TicketSaleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SalesController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $sales = TicketSale::query()
            ->where('cashier_id', $request->user()->id)
            ->with('customer')
            ->orderByDesc('sold_at')
            ->paginate(25);

        return TicketSaleResource::collection($sales);
    }
}
