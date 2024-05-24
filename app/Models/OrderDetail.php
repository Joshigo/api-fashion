<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'price_unit',
        'piece_id',
        'piece_type',
        'piece_name',
        'piece_price',
        'category_id',
        'category_name',
        'texture_id',
        'texture_name',
        // 'texture_provider',
        'color_id',
        'color_name',
        'color_code',
        'order_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
