<?php

namespace Pckg\Framework\Response\Afterware;

class EncapsulateResponse
{
    public function execute(callable $next)
    {
        if (!isHttp()) {
            return $next();
        }

        $request = request();
        if (!$request->isGet()) {
            return $next();
        }
        $router = router();
        $tags = $router->get('tags');

        if (in_array('response:raw', $tags)) {
            return $next();
        }

        $response = response();
        $output = $response->getOutput();
        if (is_string($output) && substr($output, 0, 1) === '<' && substr($output, 0, strlen('<!DOCTYPE html>')) !== '<!DOCTYPE html>') {
            $layout = config('pckg.router.layout');
            if ($layout) {
                $response->setOutput(view($layout, ['content' => $output])->autoparse());
            }
        }

        return $next();
    }
}
