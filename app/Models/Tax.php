<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    use HasFactory;
    protected $table = "taxes";
    protected $fillable = [
        'name',
        'rate',
        'status',
    ];
    protected $casts = [
        'rate' => 'decimal:2',
    ];


    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    public function getDefault()
    {
        return self::where('status', 'active')->first();
    }
    public function calculateTax($amount)
    {
        return $amount * ($this->rate / 100);
    }
    public function getFormattedRateAttribute()
    {
        return number_format($this->rate, 2) . '%';
    }
    // public function getFormattedRateAttribute()
    // {
    //     return $this->rate . '%';
    // }
    public function isActive()
    {
        return $this->status === 'active';
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
