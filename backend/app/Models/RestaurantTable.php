<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RestaurantTable extends Model
{
    protected $fillable = [
        'table_number',
        'capacity',
        'status',
        'position_x',
        'position_y',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function currentOrder()
    {
        return $this->hasOne(Order::class)->whereIn('status', ['pending', 'preparing', 'ready'])->latest();
    }

    /**
     * Get the effective table status based on active orders
     * If there are active orders, return 'occupied', otherwise return the stored status
     */
    public function getEffectiveStatusAttribute()
    {
        $activeOrder = $this->currentOrder;

        if ($activeOrder) {
            return 'occupied';
        }

        return $this->status;
    }

    /**
     * Recalculate and update table status based on active orders
     */
    public function recalculateStatus()
    {
        $effectiveStatus = $this->effective_status;

        if ($this->status !== $effectiveStatus) {
            $this->update(['status' => $effectiveStatus]);
        }
    }

    /**
     * Recalculate status for all tables
     */
    public static function recalculateAllStatuses()
    {
        $tables = self::all();

        foreach ($tables as $table) {
            $table->recalculateStatus();
        }
    }
}
