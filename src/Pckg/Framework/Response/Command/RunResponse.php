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
            $response->setOutput(json_encode($output));
        } else if (is_object($output) && method_exists($output, '__toString')) {
            $response->setOutput((string)$output);
        } else if (is_string($output) && $isAjax && get('html')) {
            $html = (string)$output;
            $vue = vueManager()->getViews();
            $response->setOutput(
                json_encode(
                    [
                        'html' => $html,
                        'vue'  => $vue,
                    ]
                )
            );
        }

        if (!$response->getOutput()) {
            $response->code(204);
        }

        echo $response->getOutput();

        return $next();
    }

}