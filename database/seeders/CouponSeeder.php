<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run()
    {
        Coupon::create([
            'code' => 'WELCOME10',
            'type' => 'percentage',
            'value' => 10,
            'min_order_value' => 50,
            'usage_limit' => 100,
            'usage_count' => 0,
            'valid_from' => now(),
            'valid_until' => now()->addMonths(3),
            'is_active' => true,
        ]);

        Coupon::create([
            'code' => 'FREESHIP',
            'type' => 'fixed',
            'value' => 15,
            'min_order_value' => 100,
            'usage_limit' => null,
            'usage_count' => 0,
            'valid_from' => now(),
            'valid_until' => now()->addMonth(),
            'is_active' => true,
        ]);

        Coupon::create([
            'code' => 'HIPHOP50',
            'type' => 'fixed',
            'value' => 50,
            'min_order_value' => 200,
            'usage_limit' => 50,
            'usage_count' => 12,
            'valid_from' => now()->subDays(7),
            'valid_until' => now()->addDays(7),
            'is_active' => true,
        ]);
    }
}
