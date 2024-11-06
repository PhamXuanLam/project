<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'parent_id', 'image', "description"
    ];

    protected $table = "categories";
    protected $primaryKey = "id";
    public $timestamps = true;
}
