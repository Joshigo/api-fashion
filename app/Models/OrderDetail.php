<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'piece_id',
        'piece_type',
        'piece_name',
        'piece_price_base',
        'piece_usage_meter_texture',
        'piece_price_total',
        'discount',
        'category_id',
        'category_name',
        'texture_id',
        'status',
        'texture_name',
        'texture_cost_meter',
        'texture_total_stock',
        'order_id',
        'texture_color_name',
        'texture_color_code',
        'piece_file_path',
        'texture_file_path',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
