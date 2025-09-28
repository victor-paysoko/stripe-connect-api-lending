<?php

namespace App\Helpers;

use Carbon\Carbon;

class IdGenerator
{
    /**
     * Generate a loan ID in format: LN-YYYY-NNNN
     * Example: LN-2025-3847, LN-2025-9012, etc.
     */
    public static function generateLoanId(): string
    {
        $currentYear = Carbon::now()->year;
        $prefix = "LN-{$currentYear}-";

        // Generate random 4-digit suffix
        $randomSuffix = str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);

        return $prefix . $randomSuffix;
    }

    /**
     * Generate a borrower ID in format: BR-NNN
     * Example: BR-347, BR-892, etc.
     */
    public static function generateBorrowerId(): string
    {
        $prefix = "BR-";

        // Generate random 3-digit suffix (100-999 to avoid very low numbers)
        $randomSuffix = random_int(100, 999);

        return $prefix . $randomSuffix;
    }

    /**
     * Generate both IDs at once
     */
    public static function generateBothIds(): array
    {
        return [
            'loan_id' => static::generateLoanId(),
            'borrower_id' => static::generateBorrowerId(),
        ];
    }

    /**
     * Validate loan ID format
     */
    public static function validateLoanId(string $loanId): bool
    {
        return preg_match('/^LN-\d{4}-\d{4}$/', $loanId);
    }

    /**
     * Validate borrower ID format
     */
    public static function validateBorrowerId(string $borrowerId): bool
    {
        return preg_match('/^BR-\d+$/', $borrowerId);
    }

    /**
     * Parse loan ID to get year and sequence
     */
    public static function parseLoanId(string $loanId): ?array
    {
        if (!static::validateLoanId($loanId)) {
            return null;
        }

        $parts = explode('-', $loanId);
        return [
            'prefix' => 'LN',
            'year' => (int) $parts[1],
            'sequence' => (int) $parts[2],
        ];
    }

    /**
     * Parse borrower ID to get sequence
     */
    public static function parseBorrowerId(string $borrowerId): ?array
    {
        if (!static::validateBorrowerId($borrowerId)) {
            return null;
        }

        $sequence = (int) str_replace('BR-', '', $borrowerId);
        return [
            'prefix' => 'BR',
            'sequence' => $sequence,
        ];
    }
}
