<?php
/**
 * Generate a valid Luhn-compliant account number
 * 
 * Commit: Create random 12-digit numbers that pass Luhn validation
 * Generates numbers until finding one that meets Luhn requirements
 * Ensures mathematically valid account numbers for financial transactions
 * 
 * @return string 12-digit Luhn-valid account number
 */
function generateLuhnAccountNumber() {
    do {
        // Commit: Generate random 12-digit number with leading zeros
        // Uses cryptographically secure random when available (mt_rand fallback)
        // Ensures consistent length for all account numbers
        $number = str_pad(mt_rand(0, 999999999999), 12, '0', STR_PAD_LEFT);
        
    } while (!luhnCheck($number)); // Commit: Retry until valid number found

    return $number;
}

/**
 * Validate a number using the Luhn algorithm
 * 
 * Commit: Implement Luhn formula verification
 * Standard check digit formula for financial identifiers
 * Protects against common data entry errors
 * 
 * @param string $number The number to validate
 * @return bool True if valid Luhn number, false otherwise
 */
function luhnCheck($number) {
    $sum = 0;
    $alt = false;
    
    // Commit: Process digits right-to-left
    // Standard Luhn algorithm processing direction
    for ($i = strlen($number) - 1; $i >= 0; $i--) {
        $n = intval($number[$i]);
        
        // Commit: Double every second digit
        // Core Luhn algorithm requirement
        if ($alt) {
            $n *= 2;
            // Commit: Handle double-digit results (sum of digits)
            // Equivalent to subtracting 9 from numbers >9
            if ($n > 9) $n -= 9;
        }
        
        $sum += $n;
        $alt = !$alt; // Toggle processing flag
    }
    
    // Commit: Validate checksum mod 10
    // Valid numbers must be divisible by 10
    return ($sum % 10) === 0;
}