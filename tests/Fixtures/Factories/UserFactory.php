<?php

namespace GoCardlessPayment\Tests\Fixtures\Factories;

use GoCardlessPayment\Tests\Fixtures\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->email(),
            'password' => bcrypt('secret'),
        ];
    }

    public function withAddress(): static
    {
        return $this->state([
            'postalcode' => fake()->postcode(),
            'address_line_1' => fake()->streetAddress(),
            'address_line_2' => fake()->streetAddress(),
            'address_line_3' => fake()->streetAddress(),
            'city' => fake()->city(),
            'region' => fake()->city(),
            'country_code' => fake()->countryCode(),
        ]);
    }

    public function withCustomerId(?string $customerId): static
    {
        return $this->state([
            config('gocardless-payment.local_customer_repositories.eloquent.key') => $customerId,
        ]);
    }
}
