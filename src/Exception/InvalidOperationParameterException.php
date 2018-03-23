<?php

namespace App\Exception;

class InvalidOperationParameterException extends \InvalidArgumentException implements AbortOperationExceptionInterface
{
}