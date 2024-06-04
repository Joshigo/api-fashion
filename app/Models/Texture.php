<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Texture extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'color_name',
        'color_code',
        'total_stock',
        'cost_meter_texture',
        'file_path',
        'piece_id',
    ];

    public function piece()
    {
        return $this->belongsTo(Piece::class);
    }

    public function textureStock()
    {
        return $this->belongsTo(TextureStockHistory::class);
    }

}
