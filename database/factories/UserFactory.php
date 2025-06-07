<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'phone' => fake()->phoneNumber(),
            'avatar_path' => null,
            'is_active' => true,
            'last_login_at' => null,
            'preferred_language' => 'fr',
            'preferences' => [],
        ];
    }

    /**
     * Configure the model factory to create a user with an organization.
     */
    public function withOrganization(): static
    {
        return $this->afterCreating(function (User $user) {
            $organization = Organization::factory()->create([
                'name' => $user->name."'s Organization",
                'email' => $user->email,
            ]);

            $user->organizations()->attach($organization->id, ['role' => 'admin']);
            $user->update(['current_organization_id' => $organization->id]);
        });
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
