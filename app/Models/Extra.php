<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Extra extends Model
{

    protected $table = 'extras';
    protected $fillable = [
        'name',
        'price',
        'category',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];


    public function orderItems()
    {
        return $this->belongsToMany(OrderItem::class, 'order_item_extras')
            ->withPivot('price_at_time')
            ->withTimestamps();
    }

    // Scope: active extras only
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->orderBy('sort_order');
    }
}
