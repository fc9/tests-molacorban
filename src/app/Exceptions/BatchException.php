<?php

namespace App\Exceptions;

use App\Libraries\Response;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Phpro\ApiProblem\Exception\ApiProblemException;
use Throwable;

class BatchException extends Exception
{
    public function __construct(Throwable $previous = null)
    {
        #dd($previous);
        if ($previous instanceof ApiProblemException) {
            $this->message = $previous->getMessage();
            $this->code = $previous->getCode();
        } elseif ($previous instanceof ModelNotFoundException) {
            $this->message = 'No query results for UUID.';
            $this->code = 404;
        } elseif ($previous instanceof QueryException) {
            $this->message = 'Invalid data query.';
            $this->code = 404;
        } elseif ($previous instanceof \Error) {
            $this->message = 'Failed to save file, contact administrator.';
            $this->code = 500;
        } else {

        }

        parent::__construct($this->message ?? $previous->getMessage(), $this->code ?? $previous->getCode(), $previous);
    }

    public function render()
    {
    }
}
