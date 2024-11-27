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
        'unit_name', 'tax_code', "position",
        'created_at', 'updated_at'
    ];

    protected $table = "users";
    protected $primaryKey = "id";
    public $timestamps = true;

    public function products()
    {
        return $this->hasMany(Product::class, 'seller_id', 'id');
    }

    public function account() {
        return $this->hasOne(Account::class, 'id', 'account_id');
    }
}
