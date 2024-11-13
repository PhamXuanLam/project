<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Variant extends Model
{
    use HasFactory;

    protected $table = "variants";
    protected $primaryKey = 'variant_id'; // Sử dụng variant_id làm khóa chính
    protected $keyType = 'string'; // Khóa chính là chuỗi
    public $incrementing = false; // Tắt tự động tăng

    protected $fillable = [
        'variant_id',
        'product_id',
        'variant_name',
        'color',
        'size',
        'style',
        'material',
        'price',
        'stock_quantity',
        'is_active'
    ];

    /**
     * Quan hệ với bảng Product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
