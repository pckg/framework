<?php namespace Pckg\Framework\Middleware;

use Throwable;

class ShowNiceErrorPage
{

    public function handle()
    {
        try {
            $output = view('Pckg/Framework:error/default');
        } catch (Throwable $e) {
            if (dev()) {
                throw $e;
            }

            $output = '<p>Error during displaying error page. Yes, not funny at all ...</p>';
        }

        if ($output) {
            return response()->setOutput($output);
        }
    }

}