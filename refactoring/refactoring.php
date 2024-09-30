<?php

class User
{
    public function __construct(
        public ?string $membershipLevel = null,
    ) {
    }

    public function isMember()
    {
        return !empty($this->membershipLevel);
    }
}

/**
 * Test Cases
 */
$user1 = new User('gold');
$user2 = new User('silver');
$user3 = new User();

$result1 = calculateDiscount($user1, 150);
$result2 = calculateDiscount($user2, 50);
$result3 = calculateDiscount($user3, 150);
$result4 = calculateDiscount($user1, 50);

$result1Refactored = calculateDiscountRefactored($user1, 150);
$result2Refactored = calculateDiscountRefactored($user2, 50);
$result3Refactored = calculateDiscountRefactored($user3, 150);
$result4Refactored = calculateDiscountRefactored($user1, 50);

/**
 * Display the results
 */
var_dump(assert($result1 === $result1Refactored, 'Test Case 1 failed'));
var_dump(assert($result2 === $result2Refactored, 'Test Case 2 failed'));
var_dump(assert($result3 === $result3Refactored, 'Test Case 3 failed'));
var_dump(assert($result4 === $result4Refactored, 'Test Case 4 failed'));

/**
 * Original function
 */
function calculateDiscount($user, $totalAmount)
{
    if ($user->isMember()) {
        if ($user->membershipLevel == 'gold') {
            if ($totalAmount > 100) {
                return $totalAmount * 0.2;
            } else {
                return $totalAmount * 0.1;
            }
        } elseif ($user->membershipLevel == 'silver') {
            return $totalAmount * 0.1;
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}

/**
 * Refactored Function
 */
function calculateDiscountRefactored(User $user, float|int $totalAmount): float|int
{
    if (!$user->isMember())
        return 0;

    return match ($user->membershipLevel) {
        'gold' => $totalAmount > 100 ? $totalAmount * 0.2 : $totalAmount * 0.1,
        'silver' => $totalAmount * 0.1,
        default => 0,
    };
}