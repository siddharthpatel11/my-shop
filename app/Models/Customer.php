<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\CustomerResetPasswordNotification;

class Customer extends Authenticatable
{

    use HasFactory, SoftDeletes, Notifiable, HasApiTokens;

    protected $table = 'customers';

    public $timestamps = true;
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'status',
        'google_id',
        'facebook_id',
        'twitter_id',
        'avatar',
        'social_provider',
        'email_otp',
        'email_otp_expires_at',
        'phone_otp',
        'phone_otp_expires_at',
        'google2fa_secret',
        'google2fa_enabled',
        'theme_mode',
        'current_session_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    /**
     * Get the default address for the customer
     */
    public function defaultAddress()
    {
        return $this->hasOne(CustomerAddress::class)->where('is_default', true);
    }
    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'customer_id');
    }

    public function wishlist()
    {
        return $this->hasMany(Wishlist::class, 'customer_id');
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomerResetPasswordNotification($token));
    }
}
