<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Piece extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'status',
        'price_base',
        'usage_meter_texture',
        'category_id',
        'file_path',
    ];

    protected $appends = ['price_total'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function textures()
    {
        return $this->hasMany(Texture::class);
    }

    public function getPriceTotalAttribute()
    {
        $costMeterTexture = $this->textures->sum('cost_meter_texture');
        return ($costMeterTexture * $this->usage_meter_texture) + $this->price_base;
    }
}
