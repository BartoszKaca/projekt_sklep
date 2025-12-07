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

    public function decreaseStock($quantity, $orderId = null)
    {
        if ($this->stock_quantity < $quantity) {
            throw new \Exception('Insufficient stock for variant');
        }
        
        $stockBefore = $this->stock_quantity;
        $this->decrement('stock_quantity', $quantity);
        
        StockMovement::create([
            'product_id' => $this->product_id,
            'product_variant_id' => $this->id,
            'order_id' => $orderId,
            'type' => 'out',
            'quantity' => $quantity,
            'stock_before' => $stockBefore,
            'stock_after' => $this->stock_quantity,
            'reason' => $orderId ? 'Zamówienie' : 'Sprzedaż',
        ]);
    }

    public function increaseStock($quantity, $reason = null, $reference = null, $userId = null)
    {
        $stockBefore = $this->stock_quantity;
        $this->increment('stock_quantity', $quantity);
        
        StockMovement::create([
            'product_id' => $this->product_id,
            'product_variant_id' => $this->id,
            'type' => 'in',
            'quantity' => $quantity,
            'stock_before' => $stockBefore,
            'stock_after' => $this->stock_quantity,
            'reason' => $reason ?? 'Dostawa',
            'reference' => $reference,
            'user_id' => $userId ?? auth()->id(),
        ]);
    }
}
