<?php

namespace GoCardlessPayment\Database\Factories;

use GoCardlessPayment\Models\GoCardlessMandate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GoCardlessMandate>
 */
class GoCardlessMandateFactory extends Factory
{
    protected $model = GoCardlessMandate::class;

    public function definition(): array
    {
        return [
            'id' => 'MD'.fake()->unique()->randomNumber(8),
            'customer_id' => 'CUS'.fake()->unique()->randomNumber(8),
            'status' => 'pending_submission',
        ];
    }
}
