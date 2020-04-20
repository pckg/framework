<?php namespace Pckg\Framework\Response;

use Pckg\Framework\Response;

class MockResponse extends Response
{

    public function stop()
    {
        trigger(Response::class . '.responded');

        $this->responded = true;

        return $this;
    }

    public function respond($string = null)
    {
        if (is_array($string)) {
            $this->output = $this->arrayToString($string);
        }

        trigger(Response::class . '.responding');

        $this->code($this->code);

        $this->stop();

        return $this;
    }

}