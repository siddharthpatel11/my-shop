<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerAddress extends Model
{
    use HasFactory, SoftDeletes;

     protected $table = 'customer_addresses';


    protected $fillable = [
        'customer_id',
        'country',
        'state',
        'district',
        'city',
        // 'area',
        'pincode',
        'full_address',
        'is_default',
        'status',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Scope a query to only include active addresses.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    protected $appends = ['formatted_address'];


    // ✅ Renamed accessor
    public function getFormattedAddressAttribute()
    {
        $parts = array_filter([
            $this->full_address,
            // $this->area,
            $this->city,
            $this->district,
            $this->state,
            $this->country,
            $this->pincode,
        ]);

        return implode(', ', $parts);
    }
}
