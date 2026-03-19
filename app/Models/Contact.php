<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'customer_id',
        'name',
        'email',
        'number',
        'message',
        'reply_message',
        'reply_image',
        'replied_at',
    ];
    protected $casts = [
        'replied_at' => 'datetime',
    ];

    /**
     * Get the customer that submitted the contact message.
     */
    public function customer()
    {
        return $this->belongsTo(\App\Models\Customer::class);
    }
}
