<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStage extends Model
{
    protected $fillable = [
        'order_id',
        'name',
        'description',
        'status',
        'sort_order',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
