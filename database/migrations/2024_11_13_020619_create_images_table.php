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
        Schema::create('images', function (Blueprint $table) {
            $table->id('image_id'); // Khóa chính cho bảng images
            $table->string('variant_id'); // FK liên kết với bảng variants
            $table->string('image_url'); // Đường dẫn hình ảnh
            $table->boolean('is_primary')->default(false); // Hình ảnh chính cho biến thể
            $table->timestamps();

            // Khóa ngoại variant_id liên kết với variant_id của bảng variants
            $table->foreign('variant_id')
                  ->references('variant_id')
                  ->on('variants')
                  ->onDelete('cascade'); // Xóa ảnh khi biến thể bị xóa
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
