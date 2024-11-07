<?php

namespace Database\Factories;

use App\Models\Variant;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class VariantFactory extends Factory
{
    protected $model = Variant::class;

    public function definition()
    {
        return [
            'variant_id' => $this->faker->unique()->uuid(), // Sử dụng UUID cho variant_id
            'product_id' => Product::inRandomOrder()->first()->product_id, // Chọn product ngẫu nhiên
            'variant_name' => $this->faker->word() . ' - ' . $this->faker->colorName(),
            'color' => $this->faker->safeColorName(),
            'size' => $this->faker->randomElement(['S', 'M', 'L', 'XL']),
            'style' => $this->faker->randomElement(['Casual', 'Formal', 'Sport']),
            'material' => $this->faker->randomElement(['Cotton', 'Polyester', 'Silk']),
            'price' => $this->faker->randomFloat(2, 10, 500),
            'stock_quantity' => $this->faker->numberBetween(1, 100),
            'is_active' => $this->faker->boolean()
        ];
    }
}
