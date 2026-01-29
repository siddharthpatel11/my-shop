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
        'type',
        'start_date',
        'end_date',
        'status'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'value' => 'decimal:2',
    ];

    public function isValid(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }
        $now = Carbon::now();
        if ($this->stat_date && $now->lt($this->start_date)) {
            return false;
        }
        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $amount): float
    {
        if (!$this->isValid()) {
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
    public function scopeValid($query)
    {
        $now = Carbon::now();

        return $query->where('status', 'active')
            ->where(function ($q) use ($now) {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', $now);
            });
    }

       public function scopeNotDeleted($query)
    {
        return $query->where('status', '!=', 'deleted');
    }
}
