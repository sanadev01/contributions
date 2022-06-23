<?php

namespace App\Pipes;

use Closure;
use App\Errors\SecondaryLabelError;

class ValidateUserBalance
{
    public function handle($order, Closure $next, $amountToCharge)
    {
        if ((float)$amountToCharge > getBalance()) {
            return new SecondaryLabelError('Not Enough Balance. Please Recharge your account.');
        }

        return $next($order);
    }
}
