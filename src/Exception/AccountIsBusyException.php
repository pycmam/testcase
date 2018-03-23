<?php
/**
 * Аккаунт в данный момент заблокирован для обновления
 */

namespace App\Exception;

class AccountIsBusyException extends \ErrorException implements RetryOperationExceptionInterface
{
}