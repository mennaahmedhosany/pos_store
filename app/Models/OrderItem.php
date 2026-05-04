<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'cup_size_id',
        'water_type_id',
        'quantity',
        'unit_price',
        'extras_price',
        'total_price',
    ];

    protected $casts = [
        'unit_price'   => 'decimal:2',
        'extras_price' => 'decimal:2',
        'total_price'  => 'decimal:2',
    ];


    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function cupSize()
    {
        return $this->belongsTo(Cupsize::class);
    }


    public function waterType()
    {
        return $this->belongsTo(WaterType::class);
    }

    public function extras()
    {
        return $this->belongsToMany(Extra::class, 'order_item_extras')
            ->withPivot('price_at_time')
            ->withTimestamps();
    }


    public function calculateTotal(): float
    {
        return ($this->unit_price + $this->extras_price) * $this->quantity;
    }
}
