<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->string('category_id')->primary(); // Khóa chính dạng chuỗi
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('parent_id')->nullable(); // ID của danh mục cha
            $table->string('image')->nullable();
            $table->timestamp('expired')->nullable();
            $table->timestamps();

            // Thiết lập khóa ngoại với onDelete cascade
            $table->foreign('parent_id')
                  ->references('category_id')
                  ->on('categories')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
