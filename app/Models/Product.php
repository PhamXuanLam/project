<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    // Tên bảng trong cơ sở dữ liệu
    protected $table = 'products';

    // Khóa chính của bảng (nếu không phải 'id')
    protected $primaryKey = 'product_id';

    // Kiểu dữ liệu của khóa chính (string)
    protected $keyType = 'string';

    public $incrementing = false; // Tắt tự động tăng

    // Cho phép Eloquent tự động quản lý thời gian created_at và updated_at
    public $timestamps = true;

    // Các thuộc tính có thể gán hàng loạt
    protected $fillable = [
        'product_id',
        'seller_id',
        'product_name',
        'description',
        'category_id',
        'min_price',
        'max_price',
        'is_approved',
        "expired",
    ];

    /**
     * Liên kết với model User, chỉ các user có position là 'seller'.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id')
                    ->where('position', 'seller');
    }

    /**
     * Liên kết với model Category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function variants()
    {
        return $this->hasMany(Variant::class, 'product_id', 'product_id');
    }
}
