<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

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
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }


}
