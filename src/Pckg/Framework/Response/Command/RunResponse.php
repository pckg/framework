<?php

namespace Pckg\Framework\Response\Command;

use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Framework\Request;
use Pckg\Framework\Response;
use Pckg\Framework\View\AbstractView;

class RunResponse extends AbstractChainOfReponsibility
{

    protected $response;

    protected $request;

    public function __construct(Response $response, Request $request)
    {
        $this->response = $response;
        $this->request = $request;
    }

    public function execute(callable $next)
    {
        $response = $this->response;
        $request = $this->request;
        $output = $response->getOutput();
        $isAjax = $request->isAjax();

        if ($output instanceof AbstractView) {
            $parsed = $output->autoparse();
            $response->setOutput($isAjax
                                     ? json_encode(array_merge($output->getData(), ['_html' => $parsed]))
                                     : $parsed);
        } else if (is_array($output)) {
            $response->setOutput($response->arrayToString($output));
        } else if (is_object($output) && method_exists($output, '__toString')) {
            $response->setOutput((string)$output);
        } else if (is_string($output) && $isAjax && get('html')) {
            $vue = vueManager()->getViews();
            $response->setOutput($response->arrayToString([
                                                              'html' => $output,
                                                              'vue'  => $vue,
                                                          ]));
        } else if (is_bool($output)) {
            $response->setOutput($response->arrayToString(['success' => $output]));
        }

        /**
         * Set empty content when no content.
         */
        if (!$response->getOutput() && substr($response->getCode(), 0, 1) === '2') {
            $response->code(204);
        }

        trigger(Response::class . '.responding');

        /**
         * Send HTTP code and Content-Type headers.
         */
        $response->sendCodeHeader();
        $response->sendTypeHeader();

        echo $response->getOutput();

        return $next();
    }
}
