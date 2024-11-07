<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'product_id' => $this->faker->unique()->uuid(), // Sử dụng UUID cho product_id
            'seller_id' => User::where('position', 'seller')->inRandomOrder()->first()->id, // Chọn seller ngẫu nhiên
            'product_name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'category_id' => Category::inRandomOrder()->first()->category_id, // Chọn category ngẫu nhiên
            'min_price' => $this->faker->randomFloat(2, 10, 1000),
            'max_price' => $this->faker->randomFloat(2, 100, 5000),
            'is_approved' => $this->faker->boolean(),
            'expired' => $this->faker->boolean() ? Carbon::now()->addDays(rand(1, 30)) : null, // Thời gian hết hạn ngẫu nhiên
        ];
    }
}

