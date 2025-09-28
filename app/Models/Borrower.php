<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Borrower extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'fi_id',                 // your internal Financial Institution id (string)
        'external_id',           // optional
        'reference_code',        // human-friendly code you show in UI

        'first_name',
        'last_name',
        'email',
        'phone',

        'address_line1',
        'address_line2',
        'address_city',
        'address_state',
        'address_postal_code',
        'address_country',

        'dob',
        'national_id',

        'default_currency',
        'status',

        'stripe_customer_id',

        'metadata',             
    ];

    /**
     * Attribute casting.
     */
    protected $casts = [
        'dob'      => 'date',
        'metadata' => 'array',
    ];

    /**
     * Optional: convenience accessors.
     */
    public function getFullNameAttribute(): string
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }
}
