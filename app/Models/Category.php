<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    use HasFactory;

    // Tên bảng trong cơ sở dữ liệu
    protected $table = 'categories';

    // Khóa chính của bảng
    protected $primaryKey = 'category_id';

    // Kiểu dữ liệu của khóa chính
    protected $keyType = 'string';

    public $incrementing = false; // Tắt tự động tăng

    // Bật quản lý thời gian created_at và updated_at
    public $timestamps = true;

    // Các thuộc tính có thể gán hàng loạt
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'parent_id',
        'image',
        'expired'
    ];

    /**
     * Quan hệ tới danh mục cha.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id', 'category_id');
    }

    /**
     * Quan hệ tới các danh mục con.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id', 'category_id');
    }
}
