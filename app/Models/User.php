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
        'jazzcash_mobile',
        'jazzcash_account_title',
        'jazzcash_verified',
        'jazzcash_merchant_id',
        'jazzcash_password',
        'jazzcash_integrity_salt',
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
            'jazzcash_verified'  => 'boolean',
            'jazzcash_password'       => 'encrypted',
            'jazzcash_integrity_salt' => 'encrypted',
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

    /** Whether this manufacturer has JazzCash configured */
    public function hasJazzCash(): bool
    {
        // Verified active if they have provided their essential routing parameters
        return !empty($this->jazzcash_mobile) && 
               !empty($this->jazzcash_merchant_id) && 
               !empty($this->jazzcash_integrity_salt);
    }
}
