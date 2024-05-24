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
        'piece_id',
        'file_path',
    ];

    public function piece()
    {
        return $this->belongsTo(Piece::class);
    }

    public function colors()
    {
        return $this->hasMany(Color::class);
    }
}
