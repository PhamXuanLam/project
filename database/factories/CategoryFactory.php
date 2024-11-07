<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition()
    {
        // Dùng một phương pháp tạo chuỗi tự động như cat001, cat002,...
        $category_id = 'cat' . str_pad($this->faker->unique()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT);

        return [
            'category_id' => $category_id, // Dùng chuỗi dạng cat001, cat002,...
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'parent_id' => $this->faker->randomElement([null, 'cat001', 'cat002']), // Thử liên kết tới một số parent_id giả
            'image' => $this->faker->imageUrl(),
            'expired' => $this->faker->boolean() ? Carbon::now()->addDays(rand(1, 60)) : null, // Thời gian hết hạn có thể là null hoặc trong khoảng 1 đến 60 ngày
        ];
    }

}
