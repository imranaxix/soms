<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'variations',
        'base_price',
    ];

    protected $casts = [
        'variations' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
