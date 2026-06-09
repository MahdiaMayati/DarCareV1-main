<?php
// app/Modules/Auth/Services/AuthService.php

namespace App\Modules\Auth\Services;

use App\Modules\Auth\Contracts\AuthServiceInterface;
use App\Modules\Auth\Http\Requests\LoginRequest;
use App\Modules\Auth\Http\Requests\RegisterProviderRequest;
use App\Modules\Auth\Http\Requests\RegisterUserRequest;
use App\Modules\Locations\Contracts\LocationServiceInterface;
use App\Modules\Users\Models\User;
use App\Modules\Providers\Models\Provider;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService implements AuthServiceInterface
{
    public function __construct(
        private readonly LocationServiceInterface $locationService
    ) {}

    public function registerUser(RegisterUserRequest $request): array
    {
        $user = User::create([
            'name'     => $request->name,
            'phone'    => $request->phone,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'user',
        ]);

        // Save primary address
        $this->locationService->createAddress($user, $request->address, isPrimary: true);

        $token = $user->createToken('auth_token')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    public function registerProvider(RegisterProviderRequest $request): array
    {
        $imagePath = $request->file('profile_image')->store('providers/images', 'public');

        $provider = Provider::create([
            'name'                => $request->name,
            'phone'               => $request->phone,
            'email'               => $request->email,
            'password'            => Hash::make($request->password),
            'years_of_experience' => $request->years_of_experience,
            'bio'                 => $request->bio,
            'profile_image'       => $imagePath,
        ]);

        $provider->categories()->sync($request->category_ids);

        $this->locationService->createAddress($provider, $request->address, isPrimary: true);

        $token = $provider->createToken('auth_token')->plainTextToken;

        return ['provider' => $provider, 'token' => $token];
    }

    public function login(LoginRequest $request, string $role): array
    {
        $model = match ($role) {
            'user'     => User::where('email', $request->email)->first(),
            'provider' => Provider::where('email', $request->email)->first(),
        };

        if (!$model || !Hash::check($request->password, $model->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $model->createToken('auth_token')->plainTextToken;

        return ['user' => $model, 'token' => $token];
    }

    public function logout($user): void
    {
        $user->currentAccessToken()->delete();
    }
}
