<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->string('product_id')->primary(); // Mã định danh sản phẩm
            $table->unsignedBigInteger('seller_id'); // Liên kết đến user id
            $table->string('product_name');
            $table->text('description')->nullable();
            $table->string('category_id'); // Liên kết đến id của categories
            $table->decimal('min_price', 10, 2);
            $table->decimal('max_price', 10, 2);
            $table->timestamps();
            $table->boolean('is_approved')->default(false); // Trạng thái phê duyệt
            $table->timestamp('expired')->nullable();

            // Định nghĩa khóa ngoại với điều kiện
            $table->foreign('seller_id')
                ->references('id')
                ->on('users')
                ->where('position', 'seller') // Điều kiện position là 'seller'
                ->onDelete('cascade');

            $table->foreign('category_id')
                ->references('category_id')
                ->on('categories')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
