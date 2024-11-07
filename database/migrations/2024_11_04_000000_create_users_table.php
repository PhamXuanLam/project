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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("account_id");
            $table->string("unit_name");
            $table->string('description')->nullable();
            $table->string("tax_code");
            $table->enum("position", ['seller', 'delivery']);
            $table->timestamps();

             // Thêm ràng buộc khóa ngoại với bảng accounts
            $table->foreign('account_id')
            ->references('id')
            ->on('accounts')
            ->onDelete('cascade'); // Xóa user nếu account bị xóa
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Xóa khóa ngoại trước khi xóa bảng
            $table->dropForeign(['account_id']);
        });

        Schema::dropIfExists('users');
    }
};
