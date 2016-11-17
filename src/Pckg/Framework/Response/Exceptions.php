<?php

namespace Pckg\Framework\Response;

use Exception;
use Pckg\Framework\Exception\NotFound;

trait Exceptions
{

    public function none($message = 'Empty response')
    {
        $this->exception($message, 400);
    }

    public function exception($message, $code = 400, $class = Exception::class)
    {
        if ($code) {
            $this->code = $code;
        }

        $up = new $class($message, $code);

        //d($up->getTraceAsString());

        throw $up;
    }

    public function bad($message = 'Bad request')
    {
        $this->exception($message, 400);
    }

    public function unauthorized($message = 'Unauthorized')
    {
        $this->exception($message, 401);
    }

    public function forbidden($message = 'Forbidden')
    {
        $this->exception($message, 403);
    }

    public function notFound($message = 'Not found')
    {
        $this->exception($message, 404, NotFound::class);
    }

    public function fatal($message = 'Fatal error')
    {
        $this->exception($message, 500);
    }

    public function unavailable($message = 'Service unavailable')
    {
        $this->exception($message, 503);
    }

}