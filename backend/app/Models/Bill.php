<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bill extends Model
{
    protected $fillable = [
        'order_id',
        'bill_number',
        'subtotal',
        'tax_amount',
        'tax_percentage',
        'service_charge',
        'discount_amount',
        'discount_percentage',
        'total_amount',
        'amount_paid',
        'payment_method',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bill) {
            if (!$bill->bill_number) {
                $bill->bill_number = 'BILL-' . strtoupper(uniqid());
            }
        });
    }
}
