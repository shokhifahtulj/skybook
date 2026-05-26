<?php

namespace App\Exceptions;

use Exception;

class SeatUnavailableException extends Exception
{
    protected $message = 'Kursi ini tidak tersedia atau sudah dipesan.';
}
