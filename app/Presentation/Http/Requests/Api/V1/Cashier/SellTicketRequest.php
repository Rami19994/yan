<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests\Api\V1\Cashier;

use Illuminate\Foundation\Http\FormRequest;

class SellTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:30'],
        ];
    }
}
