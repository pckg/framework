<?php

namespace Pckg\Framework\Response;

trait Exceptions {

    public function none($message = 'Empty response')
    {
        $this->exception($message, 400);
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
        $this->exception($message, 404);
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