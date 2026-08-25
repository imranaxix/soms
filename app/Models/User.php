<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'business_name',
        'role',
        'profile_image',
        'stripe_connect_id',
        'stripe_onboarding_completed',
        'stripe_publishable_key',
        'stripe_secret_key',
        'safepay_api_key',
        'safepay_secret_key',
        'safepay_webhook_secret',
        'safepay_environment',
        'is_active',
        'is_verified',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'  => 'datetime',
            'password'           => 'hashed',
            'stripe_onboarding_completed' => 'boolean',
            'stripe_publishable_key'      => 'encrypted',
            'stripe_secret_key'           => 'encrypted',
            'safepay_api_key'             => 'encrypted',
            'safepay_secret_key'          => 'encrypted',
            'safepay_webhook_secret'      => 'encrypted',
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
        ];
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function manufacturerConnections()
    {
        return $this->hasMany(Connection::class, 'shop_owner_id');
    }

    public function shopOwnerConnections()
    {
        return $this->hasMany(Connection::class, 'manufacturer_id');
    }

    public function connections()
    {
        return $this->role === 'manufacturer'
            ? $this->shopOwnerConnections()
            : $this->manufacturerConnections();
    }

    /** Payments made by this user (shop owner outgoing) */
    public function paymentsMade()
    {
        return $this->hasMany(Payment::class, 'payer_id');
    }

    /** Payments received by this user (manufacturer incoming) */
    public function paymentsReceived()
    {
        return $this->hasMany(Payment::class, 'payee_id');
    }

    /** Whether this manufacturer has Stripe configured */
    public function hasStripe(): bool
    {
        return !empty($this->stripe_publishable_key) && !empty($this->stripe_secret_key);
    }

    /** Whether this manufacturer has Safepay configured */
    public function hasSafepay(): bool
    {
        return !empty($this->safepay_api_key) && !empty($this->safepay_secret_key);
    }
}
