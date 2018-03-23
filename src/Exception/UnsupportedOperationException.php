<?php

namespace App\Exception;

class UnsupportedOperationException extends \InvalidArgumentException implements AbortOperationExceptionInterface
{
}