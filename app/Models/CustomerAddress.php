<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    use HasFactory;

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
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
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
