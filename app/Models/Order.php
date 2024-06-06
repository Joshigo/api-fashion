<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'status',
        'neck',
        'shoulder',
        'arm',
        'mid_front',
        'bicep',
        'bust',
        'size',
        'waist',
        'leg',
        'hip',
        'skirt_length',
        'unit_length',
        'total_price',
        'total_texture',
        'user_id',
    ];

    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }


}
