<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderShipping extends Model
{
    use HasFactory;

    protected $table = 'order_shipping';

    protected $fillable = [
        'order_id', 'first_name', 'last_name', 'street_address', 
        'apartment', 'city', 'postal_code', 'country', 'phone', 'email'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}