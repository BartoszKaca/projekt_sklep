<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
     use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'type', 'price', 
        'discount_price', 'artist', 'release_year', 'format', 'label', 
        'stock_quantity', 'low_stock_threshold', 'sku', 'barcode', 
        'is_featured', 'is_active', 'views_count', 'weight'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'release_year' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'low_stock_threshold');
    }

    public function scopeAlbums($query)
    {
        return $query->where('type', 'album');
    }

    public function scopeMerch($query)
    {
        return $query->where('type', 'merch');
    }

    // Helpers
    public function isLowStock()
    {
        return $this->stock_quantity <= $this->low_stock_threshold;
    }

    public function isInStock()
    {
        return $this->stock_quantity > 0;
    }

    public function getFinalPrice()
    {
        return $this->discount_price ?? $this->price;
    }

    public function getDiscountPercentage()
    {
        if (!$this->discount_price) return 0;
        return round((($this->price - $this->discount_price) / $this->price) * 100);
    }

    public function decreaseStock($quantity)
    {
        if ($this->stock_quantity < $quantity) {
            throw new \Exception('Insufficient stock');
        }
        
        $this->decrement('stock_quantity', $quantity);
        
        StockMovement::create([
            'product_id' => $this->id,
            'type' => 'out',
            'quantity' => $quantity,
            'stock_before' => $this->stock_quantity + $quantity,
            'stock_after' => $this->stock_quantity,
        ]);
    }

    public function increaseStock($quantity, $reason = null)
    {
        $this->increment('stock_quantity', $quantity);
        
        StockMovement::create([
            'product_id' => $this->id,
            'type' => 'in',
            'quantity' => $quantity,
            'stock_before' => $this->stock_quantity - $quantity,
            'stock_after' => $this->stock_quantity,
            'reason' => $reason,
        ]);
    }
}
