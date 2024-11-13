<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $primaryKey = 'image_id';
    protected $fillable = ['variant_id', 'image_url', 'is_primary'];

    public function variant()
    {
        return $this->belongsTo(Variant::class, 'variant_id');
    }
}
