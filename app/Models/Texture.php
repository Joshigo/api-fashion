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
    ];

    public function piece()
    {
        return $this->belongsTo(Piece::class);
    }
}
