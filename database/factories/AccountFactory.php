<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition()
    {
        return [
            'username' => $this->faker->userName,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->unique()->phoneNumber,
            'avatar' => $this->faker->imageUrl(),
            'password' => bcrypt('password'), // Đặt mật khẩu mặc định
            'role' => $this->faker->randomElement(['buyer']),
            'address' => $this->faker->address,
            'remember_token' => Str::random(10),
        ];
    }
}
