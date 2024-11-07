<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run()
    {
        // Chèn các danh mục cha trước
        DB::table('categories')->insert([
            'category_id' => 'cat001',
            'name' => 'Category 1',
            'description' => 'This is category 1',
            'parent_id' => null,
            'image' => 'https://via.placeholder.com/640x480.png/00bb99?text=Category+1',
            'expired' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('categories')->insert([
            'category_id' => 'cat002',
            'name' => 'Category 2',
            'description' => 'This is category 2',
            'parent_id' => null,
            'image' => 'https://via.placeholder.com/640x480.png/00bb99?text=Category+2',
            'expired' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Chèn các danh mục con sau khi các danh mục cha đã có
        DB::table('categories')->insert([
            'category_id' => 'cat003',
            'name' => 'Subcategory of Category 1',
            'description' => 'This is a subcategory of category 1',
            'parent_id' => 'cat001', // Đảm bảo sử dụng category_id hợp lệ
            'image' => 'https://via.placeholder.com/640x480.png/00bb99?text=fuga',
            'expired' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('categories')->insert([
            'category_id' => 'cat004',
            'name' => 'Subcategory of Category 2',
            'description' => 'This is a subcategory of category 2',
            'parent_id' => 'cat002', // Đảm bảo sử dụng category_id hợp lệ
            'image' => 'https://via.placeholder.com/640x480.png/00bb99?text=fuga',
            'expired' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
