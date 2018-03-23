<?php
/**
 * Недостаточно средств на балансе
 */

namespace App\Exception;

class NotEnoughBalanceException extends \ErrorException implements AbortOperationExceptionInterface
{
}