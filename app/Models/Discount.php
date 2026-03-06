<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $table = 'discounts';
    protected $fillable = [
        'code',
        'value',
        'min_amount',
        'type',
        'start_date',
        'end_date',
        'status'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'value' => 'decimal:2',
        'min_amount' => 'decimal:2',
    ];

    public function isValid(float $amount = null): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($amount !== null && $amount < $this->min_amount) {
            return false;
        }

        $now = Carbon::now();
        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }
        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $amount): float
    {
        if (!$this->isValid($amount)) {
            return 0;
        }
        $discount = 0;

        if ($this->type === 'percentage') {
            $discount = ($amount * $this->value) / 100;
        } else {
            $discount = $this->value;
        }
        return min($discount, $amount);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    public function scopeValid($query, float $amount = null)
    {
        $now = Carbon::now();

        $query->where('status', 'active')
            ->where(function ($q) use ($now) {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', $now);
            });

        if ($amount !== null) {
            $query->where('min_amount', '<=', $amount);
        }

        return $query;
    }

    public function scopeNotDeleted($query)
    {
        return $query->where('status', '!=', 'deleted');
    }
}
