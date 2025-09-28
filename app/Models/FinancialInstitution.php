<?php
// app/Models/FinancialInstitution.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialInstitution extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'stripe_account_id',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    // Relationships
    public function repaymentAttempts()
    {
        return $this->hasMany(RepaymentAttempt::class, 'financial_institution_id');
    }

    public function borrowers()
    {
        return $this->hasMany(Borrower::class, 'financial_institution_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithStripeAccount($query)
    {
        return $query->whereNotNull('stripe_account_id');
    }

    // Helpers
    public function isStripeConnected(): bool
    {
        return !empty($this->stripe_account_id);
    }

    public function getStripeAccountId(): ?string
    {
        return $this->stripe_account_id;
    }
}
