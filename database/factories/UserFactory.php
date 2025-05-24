<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories.Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Factory tarafından kullanılacak mevcut şifre.
     */
    protected static ?string $password;

    /**
     * Modelin varsayılan durumunu tanımlar.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(), // Rastgele isim üretir
            'email' => fake()->unique()->safeEmail(), // Benzersiz ve güvenli rastgele e-posta üretir
            'email_verified_at' => now(), // E-posta doğrulama zamanı olarak şu anı atar
            'password' => static::$password ??= Hash::make('password'), // Şifreyi hash'ler (varsayılan: 'password')
            'remember_token' => Str::random(10), // 10 karakterlik rastgele bir token üretir
        ];
    }

    /**
     * Modelin e-posta adresinin doğrulanmamış olmasını belirtir.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null, // E-posta doğrulama zamanını null yapar (doğrulanmamış)
        ]);
    }
}
