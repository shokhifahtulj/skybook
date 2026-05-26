<?php

namespace App\Exceptions;

use Exception;

class SeatAlreadyLockedException extends Exception
{
    protected $message = 'Kursi ini sedang dikunci oleh pengguna lain.';
}
