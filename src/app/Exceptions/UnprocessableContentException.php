<?php

namespace App\Exceptions;

use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UnprocessableContentException extends HttpException
{
    public function __construct($mess = 'Unprocessable Content.')
    {
        parent::__construct(Response::HTTP_UNPROCESSABLE_ENTITY, $mess);
    }
}
