<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TextureStockHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount','texture_id','total_stock',
    ];

    public function textures()
    {
        return $this->hasMany(Texture::class);
    }
}
