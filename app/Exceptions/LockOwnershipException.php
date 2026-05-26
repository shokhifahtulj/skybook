<?php

namespace App\Exceptions;

use Exception;

class LockOwnershipException extends Exception
{
    protected $message = 'Anda tidak memiliki hak akses pada status kursi ini (Ownership Invalid).';
}
