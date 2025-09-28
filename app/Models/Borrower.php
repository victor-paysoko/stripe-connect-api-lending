<?php
// app/Models/Borrower.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Borrower extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'financial_institution_id',
        'external_borrower_id',
        'email',
        'name',
        'phone',
        'stripe_customer_id',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    // Relationships
    public function financialInstitution()
    {
        return $this->belongsTo(FinancialInstitution::class, 'financial_institution_id');
    }

    public function repaymentAttempts()
    {
        return $this->hasMany(RepaymentAttempt::class, 'borrower_id', 'external_borrower_id')
            ->where('financial_institution_id', $this->financial_institution_id);
    }

    // Scopes
    public function scopeForFinancialInstitution($query, $fiId)
    {
        return $query->where('financial_institution_id', $fiId);
    }

    public function scopeWithStripeCustomer($query)
    {
        return $query->whereNotNull('stripe_customer_id');
    }

    public function scopeByEmail($query, $email)
    {
        return $query->where('email', $email);
    }

    // Helpers
    public function hasStripeCustomer(): bool
    {
        return !empty($this->stripe_customer_id);
    }

    public function getStripeCustomerId(): ?string
    {
        return $this->stripe_customer_id;
    }

    public function linkStripeCustomer(string $customerId): void
    {
        $this->update(['stripe_customer_id' => $customerId]);
    }
}
