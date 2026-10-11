<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Api\V1\Auth;

use App\Domain\Audit\Services\AuditLogger;
use App\Domain\User\Models\User;
use App\Presentation\Http\Controllers\Controller;
use App\Presentation\Http\Requests\Api\V1\Auth\ApiLoginRequest;
use App\Presentation\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuditLogger $auditLogger,
    ) {}

    public function login(ApiLoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        /** @var User|null $user */
        $user = User::query()->where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => [__('This account has been deactivated.')],
            ]);
        }

        $token = $user->createToken($data['device_name']);

        $this->auditLogger->log('Login', $user, [], ['via' => 'api'], $user->id);

        return response()->json([
            'token' => $token->plainTextToken,
            'user' => new UserResource($user),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $this->auditLogger->log('Logout', $user, [], ['via' => 'api'], $user->id);

        // auth:sanctum middleware guarantees a token authenticated this
        // request, so currentAccessToken() is never null here.
        $user->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out.']);
    }

    public function me(Request $request): UserResource
    {
        return new UserResource($request->user());
    }
}
