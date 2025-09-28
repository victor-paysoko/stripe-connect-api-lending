<?php
// app/Models/RepaymentAttempt.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class RepaymentAttempt extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'financial_institution_id',
        'loan_id',
        'borrower_id',
        'installment_number',
        'amount_due',
        'currency',
        'platform_fee_amount',
        'net_amount',
        'stripe_payment_intent_id',
        'stripe_transfer_id',
        'status',
        'failure_reason',
        'idempotency_key',
        'metadata',
    ];

    protected $casts = [
        'amount_due' => 'integer',
        'platform_fee_amount' => 'integer',
        'net_amount' => 'integer',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_REQUIRES_ACTION = 'requires_action';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SUCCEEDED = 'succeeded';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELED = 'canceled';

    // Relationships
    public function financialInstitution()
    {
        return $this->belongsTo(FinancialInstitution::class, 'financial_institution_id');
    }

    public function borrower()
    {
        return $this->belongsTo(Borrower::class, 'borrower_id', 'external_borrower_id')
            ->where('financial_institution_id', $this->financial_institution_id);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', self::STATUS_PROCESSING);
    }

    public function scopeSucceeded($query)
    {
        return $query->where('status', self::STATUS_SUCCEEDED);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public function scopeForFinancialInstitution($query, $fiId)
    {
        return $query->where('financial_institution_id', $fiId);
    }

    public function scopeForLoan($query, $loanId)
    {
        return $query->where('loan_id', $loanId);
    }

    // Helpers
    public function isFinal(): bool
    {
        return in_array($this->status, [self::STATUS_SUCCEEDED, self::STATUS_FAILED, self::STATUS_CANCELED]);
    }

    public function canRetry(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function markAsSucceeded(): void
    {
        $this->update(['status' => self::STATUS_SUCCEEDED, 'failure_reason' => null]);
    }

    public function markAsFailed(string $reason): void
    {
        $this->update(['status' => self::STATUS_FAILED, 'failure_reason' => $reason]);
    }

    public function getAmountInDollars(): float
    {
        return $this->amount_due / 100;
    }

    public function getPlatformFeeInDollars(): float
    {
        return $this->platform_fee_amount / 100;
    }
}
