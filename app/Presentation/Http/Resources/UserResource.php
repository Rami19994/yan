<?php

declare(strict_types=1);

namespace App\Presentation\Http\Resources;

use App\Domain\User\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->getRoleNames()->first(),
            'is_active' => $this->is_active,
            'master_id' => $this->master_id,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
