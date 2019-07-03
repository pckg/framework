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

            $output = '<p>No page found.</p>';
        }

        if ($output) {
            return response()->setOutput($output);
        }
    }

    public function handlePartial()
    {
        try {
            $output = view('Pckg/Framework:error/_partial');
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