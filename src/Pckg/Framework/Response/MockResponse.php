<?php

namespace Pckg\Framework\Response;

use Pckg\Framework\Response;

class MockResponse extends Response
{

    protected $redirected = null;

    public function stop($code = null)
    {
        trigger(Response::class . '.responded');

        $this->responded = true;

        throw new MockStop('Response STOP');

        return $this;
    }

    public function respond($string = null)
    {
        if (is_array($string)) {
            $this->output = $this->arrayToString($string);
        } else if ($string) {
            $this->output = $string;
        }

        trigger(Response::class . '.responding');

        $this->stop();

        return $this;
    }


    public function redirect($url = null, $routerParams = [], $httpParams = [])
    {
        $code = $this->getCode();
        if ($code == 200) {
            $this->code(301);
        }

        $this->redirected = $url;

        return $this;
    }
}
