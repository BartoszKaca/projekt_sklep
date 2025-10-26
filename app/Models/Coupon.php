<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'type', 'value', 'min_order_value', 'usage_limit', 
        'usage_count', 'valid_from', 'valid_until', 'is_active'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_value' => 'decimal:2',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function isValid($orderTotal = 0)
    {
        if (!$this->is_active) return false;
        if ($this->valid_from && now()->isBefore($this->valid_from)) return false;
        if ($this->valid_until && now()->isAfter($this->valid_until)) return false;
        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) return false;
        if ($this->min_order_value && $orderTotal < $this->min_order_value) return false;
        
        return true;
    }

    public function calculateDiscount($amount)
    {
        if ($this->type === 'percentage') {
            return ($amount * $this->value) / 100;
        }
        
        return min($this->value, $amount);
    }
}