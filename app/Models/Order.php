<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number', 'user_id', 'status', 'subtotal', 'shipping_cost', 
        'tax', 'discount', 'total', 'coupon_code', 'payment_method', 
        'payment_status', 'customer_notes', 'admin_notes', 'tracking_number', 
        'carrier', 'payu_order_id', 'paid_at', 'shipped_at', 'delivered_at'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function shipping()
    {
        return $this->hasOne(OrderShipping::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }


    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', '!=', 'paid');
    }


    public static function generateOrderNumber()
    {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }


    public function markAsPaid()
    {
        $this->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);
    }


    public function markAsShipped($trackingNumber = null, $carrier = null)
    {
        $this->update([
            'status' => 'shipped',
            'tracking_number' => $trackingNumber,
            'carrier' => $carrier,
            'shipped_at' => now(),
        ]);
    }


    public function markAsDelivered()
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }


    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }


    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    public function markAsCancelled($reason = null)
    {
        $this->update([
            'status' => 'cancelled',
            'admin_notes' => ($this->admin_notes ? $this->admin_notes . '\n' : '') . 'Anulowane. ' . ($reason ?? ''),
        ]);
    }

    public function markAsRefunded($reason = null)
    {
        $this->update([
            'status' => 'refunded',
            'payment_status' => 'refunded',
            'admin_notes' => ($this->admin_notes ? $this->admin_notes . '\n' : '') . 'Zwrot. ' . ($reason ?? ''),
        ]);
    }
}
