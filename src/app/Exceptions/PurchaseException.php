<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

class PurchaseException extends Exception
{
    public function __construct(Throwable $previous = null)
    {
        #dd($previous);
        if ($previous instanceof \Error) {
            $message = 'Failed to save file, contact administrator.';
            $code = 500;
        } elseif ($previous instanceof ModelNotFoundException) {
            $message = 'Entry for ' . str_replace('App', '', $previous->getModel()) . ' not found';
            $code = 404;
        }

        parent::__construct($message ?? $previous->getMessage(), $code ?? $previous->getCode(), $previous);
    }

    public function render()
    {
    }
}
