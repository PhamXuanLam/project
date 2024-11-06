<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'account_id', 'description',
        'unit_name', 'tax_code',
        'created_at', 'updated_at'
    ];

    protected $table = "users";
    protected $primaryKey = "id";
    public $timestamps = true;
}
