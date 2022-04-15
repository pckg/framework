<?php

namespace Pckg\Framework\Middleware;

use Throwable;

class ShowNiceErrorPage
{
    public function handle()
    {
        try {
            $layout = config('pckg.framework.error.layout', 'Pckg/Framework:error/default');
            $output = view($layout);
        } catch (Throwable $e) {
            if (dev()) {
                throw $e;
            }

            $output = '<p>No page found.</p>';
        }

        if ($output) {
            return response()->setOutput($output);
        }
    }

    public function handlePartial()
    {
        try {
            $layout = config('pckg.framework.error.layout', 'Pckg/Framework:error/default');
            $extends = config('pckg.framework.error.extends', 'Pckg/Generic/View/error.twig');
            $output = view($layout, [
                'extends' => $extends,
            ]);
        } catch (Throwable $e) {
            if (dev()) {
                throw $e;
            }

            $output = '<p>No page found.</p>';
        }

        return $output;
    }

    public function handleJson($data = [])
    {
        try {
            return array_merge([
                'success' => false,
                'error'   => true,
                'message' => 'Error',
            ], $data);
        } catch (Throwable $e) {
        }
    }
}
