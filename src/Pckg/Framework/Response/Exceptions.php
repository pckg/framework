<?php

namespace Pckg\Framework\Response;

use Exception;
use Pckg\Framework\Exception\NotFound;
use Throwable;

trait Exceptions
{

    public function none($message = 'Empty response')
    {
        $this->exception($message, 400);
    }

    public function exception($message, $code = 400, $class = Exception::class)
    {
        if (!$class) {
            $class = Exception::class;
        }
        
        if ($code) {
            $this->code = $code;
        }

        if (is_string($class)) {
            $up = new $class($message, $code);

        } else {
            $up = $class;

        }

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

    public function unavailable($message = 'Service unavailable', Throwable $e = null)
    {
        $this->exception($message, 503, $e);
    }

}