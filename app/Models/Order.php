<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; 

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'shop_owner_id',
        'manufacturer_id',
        'product_id',
        'variant_id',
        'quantity',
        'unit',
        'total_amount',
        'paid_amount',
        'payment_terms',
        'due_date',
        'status',
        'progress_percent',
        'special_instructions',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function shopOwner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shop_owner_id');
    }

    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manufacturer_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function stages()
    {
        return $this->hasMany(OrderStage::class)->orderBy('sort_order');
    }
}
