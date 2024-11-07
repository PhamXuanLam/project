<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('variants', function (Blueprint $table) {
            $table->string('variant_id')->primary(); // Khóa chính cho bảng variants
            $table->string('product_id'); // FK liên kết với bảng products
            $table->string('variant_name');
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->string('style')->nullable();
            $table->string('material')->nullable();
            $table->decimal('price', 10, 2); // Giá bán cho biến thể
            $table->integer('stock_quantity')->default(0); // Số lượng tồn kho
            $table->boolean('is_active')->default(true); // Trạng thái kích hoạt của biến thể
            $table->timestamps();

            // Khóa ngoại product_id liên kết với id của bảng products
            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('products')
                  ->onDelete('cascade'); // Xóa biến thể khi sản phẩm bị xóa
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('variants');
    }
};
