<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KitchenOrderTicket extends Model
{
    protected $fillable = [
        'order_id',
        'kot_number',
        'status',
        'notes',
        'printed_at',
    ];

    protected $casts = [
        'printed_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($kot) {
            if (!$kot->kot_number) {
                $kot->kot_number = 'KOT-' . strtoupper(uniqid());
            }
        });
    }
}
