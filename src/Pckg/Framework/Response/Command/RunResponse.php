<?php

namespace Pckg\Framework\Response\Command;

use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Framework\Request;
use Pckg\Framework\Response;
use Pckg\Framework\View\AbstractView;
use Pckg\Framework\View\Twig;

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

        if ($output instanceof AbstractView || $output instanceof Twig) {
            if ($request->isAjax()) {
                $response->setOutput(json_encode(array_merge($output->getData(), ['_html' => $output->autoparse()])));
            } else {
                $response->setOutput($output->autoparse());
            }
        } else if (is_array($output)) {
            $response->setOutput(json_encode($output));
        } else if (is_object($output) && method_exists($output, '__toString')) {
            $response->setOutput((string)$output);
        } else if (is_string($output)) {
            if ($request->isAjax()/* && strpos($output, '[') !== 0 && strpos($output, '{') !== 0*/) {
                //$this->setOutput(json_encode(['_html' => $output]));
                if (get('html')) {
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
            }
        }

        if (!$output) {
            $response->none();
        }

        echo $output;

        return $next();
    }

}