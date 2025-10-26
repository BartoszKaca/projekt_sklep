<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'name', 'size', 'color', 'price_modifier', 
        'stock_quantity', 'sku', 'is_active'
    ];

    protected $casts = [
        'price_modifier' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function getFinalPrice()
    {
        return $this->product->getFinalPrice() + $this->price_modifier;
    }

    public function isInStock()
    {
        return $this->stock_quantity > 0;
    }
}
