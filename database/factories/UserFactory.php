<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'account_id' => Account::factory(),
            'unit_name' => $this->faker->company,
            'description' => $this->faker->sentence,
            'tax_code' => $this->faker->unique()->numerify('#########'),
            'position' => $this->faker->randomElement(['delivery']),
        ];
    }
}
